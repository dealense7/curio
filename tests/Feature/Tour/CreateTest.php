<?php

declare(strict_types=1);

use App\Enums\General\Currency as CurrencyEnum;
use App\Enums\General\Difficulty as DifficultyEnum;
use App\Enums\General\FileType;
use App\Enums\General\Month as MonthEnum;
use App\Enums\General\PublishingStatus as PublishingStatusEnum;
use App\Models\General\File;
use App\Models\Tour\Tour;
use App\Support\Testing\ProvidesTestingData;
use Tests\Feature\Tour\ModelTestCase;

uses(ModelTestCase::class);

it('should raise unauthorized', function (): void {
    $response = $this->jsonWithHeader('POST', $this->url('admin/tours'));

    $response->assertUnauthorized();
});

it('should raise validation errors', function (): void {
    ProvidesTestingData::createRandomUserAndAuthorize();

    $response = $this->jsonWithHeader('POST', $this->url('admin/tours'));

    $response->assertJsonValidationErrors([
        'title',
        'description',
        'start_location',
        'end_location',
        'distance_km',
        'duration_comfortable_days',
        'duration_recommended_days',
        'daily_distance_comfortable_km',
        'daily_distance_recommended_km',
        'elevation_gain_m',
        'difficulty_id',
        'price_comfortable',
        'price_recommended',
        'average_daily_spend',
        'currency_id',
        'best_month_ids',
        'route_file_id',
        'cover_image_id',
        'publishing_status_id',
    ]);
});

it('should validate lookup ids, file types, and unique months', function (): void {
    ProvidesTestingData::createRandomUserAndAuthorize([], ['permissions' => $this->getPermissions(['create'])]);
    /** @var File $wrongRoute */
    $wrongRoute = ProvidesTestingData::createFileRandomItem(['type' => FileType::IMAGE])->first();
    /** @var File $wrongCover */
    $wrongCover = ProvidesTestingData::createFileRandomItem(['type' => FileType::ROUTE])->first();
    /** @var Month $month */
    $month      = ProvidesTestingData::createMonthRandomItem(['key' => MonthEnum::JUNE, 'sort_order' => 6])->first();

    $response = $this->jsonWithHeader('POST', $this->url('admin/tours'), [
        'title'                         => 'Invalid Tour',
        'description'                   => 'Invalid lookup and file values.',
        'start_location'                => 'Start',
        'end_location'                  => 'End',
        'distance_km'                   => 10,
        'duration_comfortable_days'     => 2,
        'duration_recommended_days'     => 1,
        'daily_distance_comfortable_km' => 50,
        'daily_distance_recommended_km' => 100,
        'elevation_gain_m'              => 100,
        'difficulty_id'                 => 999999,
        'price_comfortable'             => 100,
        'price_recommended'             => 150,
        'average_daily_spend'           => 50,
        'currency_id'                   => 999999,
        'best_month_ids'                => [$month->getId(), $month->getId()],
        'route_file_id'                 => $wrongRoute->getPublicId(),
        'cover_image_id'                => $wrongCover->getPublicId(),
        'publishing_status_id'          => 999999,
    ]);

    $response->assertJsonValidationErrors([
        'difficulty_id',
        'currency_id',
        'best_month_ids.1',
        'route_file_id',
        'cover_image_id',
        'publishing_status_id',
    ]);
});

it('should raise forbidden', function (): void {
    ProvidesTestingData::createRandomUserAndAuthorize();
    /** @var Difficulty $difficulty */
    $difficulty           = ProvidesTestingData::createDifficultyRandomItem()->first();
    /** @var Currency $currency */
    $currency             = ProvidesTestingData::createCurrencyRandomItem()->first();
    /** @var PublishingStatus $publishingStatus */
    $publishingStatus     = ProvidesTestingData::createPublishingStatusRandomItem()->first();
    /** @var Month $month */
    $month                = ProvidesTestingData::createMonthRandomItem()->first();
    /** @var File $route */
    $route                = ProvidesTestingData::createFileRandomItem(['type' => FileType::ROUTE])->first();
    /** @var File $cover */
    $cover                = ProvidesTestingData::createFileRandomItem(['type' => FileType::IMAGE])->first();

    $response = $this->jsonWithHeader('POST', $this->url('admin/tours'), [
        'title'                         => 'Forbidden Tour',
        'description'                   => 'A valid request without permission.',
        'start_location'                => 'Start',
        'end_location'                  => 'End',
        'distance_km'                   => 10,
        'duration_comfortable_days'     => 2,
        'duration_recommended_days'     => 1,
        'daily_distance_comfortable_km' => 50,
        'daily_distance_recommended_km' => 100,
        'elevation_gain_m'              => 100,
        'difficulty_id'                 => $difficulty->getId(),
        'price_comfortable'             => 100,
        'price_recommended'             => 150,
        'average_daily_spend'           => 50,
        'currency_id'                   => $currency->getId(),
        'best_month_ids'                => [$month->getId()],
        'route_file_id'                 => $route->getPublicId(),
        'cover_image_id'                => $cover->getPublicId(),
        'publishing_status_id'          => $publishingStatus->getId(),
    ]);

    $response->assertForbiddenPermissions($this->getPermissions(['create']));
    $response->assertForbidden();
});

it('should create a tour with lookup and file relations', function (): void {
    ProvidesTestingData::createRandomUserAndAuthorize([], ['permissions' => $this->getPermissions(['create'])]);
    /** @var Difficulty $difficulty */
    $difficulty           = ProvidesTestingData::createDifficultyRandomItem(['key' => DifficultyEnum::CHALLENGING])->first();
    /** @var Currency $currency */
    $currency             = ProvidesTestingData::createCurrencyRandomItem(['code' => CurrencyEnum::USD])->first();
    /** @var PublishingStatus $publishingStatus */
    $publishingStatus     = ProvidesTestingData::createPublishingStatusRandomItem(['key' => PublishingStatusEnum::PUBLISHED])->first();
    /** @var Month $june */
    $june                 = ProvidesTestingData::createMonthRandomItem(['key' => MonthEnum::JUNE, 'sort_order' => 6])->first();
    /** @var Month $september */
    $september            = ProvidesTestingData::createMonthRandomItem(['key' => MonthEnum::SEPTEMBER, 'sort_order' => 9])->first();
    /** @var File $route */
    $route                = ProvidesTestingData::createFileRandomItem(['type' => FileType::ROUTE])->first();
    /** @var File $cover */
    $cover                = ProvidesTestingData::createFileRandomItem(['type' => FileType::IMAGE])->first();

    $response = $this->jsonWithHeader('POST', $this->url('admin/tours'), [
        'title'                         => 'Kazbegi Mountain Traverse',
        'description'                   => 'A multi-day mountain route through the Greater Caucasus.',
        'start_location'                => 'Stepantsminda',
        'end_location'                  => 'Juta',
        'distance_km'                   => '42.50',
        'duration_comfortable_days'     => 4,
        'duration_recommended_days'     => 3,
        'daily_distance_comfortable_km' => 75.5,
        'daily_distance_recommended_km' => 100.67,
        'elevation_gain_m'              => 1850,
        'difficulty_id'                 => $difficulty->getId(),
        'price_comfortable'             => 1250,
        'price_recommended'             => 1800,
        'average_daily_spend'           => 85,
        'currency_id'                   => $currency->getId(),
        'best_month_ids'                => [$june->getId(), $september->getId()],
        'route_file_id'                 => $route->getPublicId(),
        'cover_image_id'                => $cover->getPublicId(),
        'publishing_status_id'          => $publishingStatus->getId(),
    ]);

    $response->assertOk();
    $response->assertJsonDataItemStructure($this->getTourStructure([
        'difficulty',
        'currency',
        'publishingStatus',
        'routeFile:file',
        'coverImage:file',
        '[bestMonths:month]',
    ]));

    $tour = Tour::query()->where('public_id', $response->json('data.id'))->firstOrFail();

    $this->assertDatabaseHas($tour->getTable(), [
        'id'                   => $tour->getId(),
        'title'                => 'Kazbegi Mountain Traverse',
        'difficulty_id'        => $difficulty->getId(),
        'currency_id'          => $currency->getId(),
        'route_file_id'        => $route->getId(),
        'cover_image_id'       => $cover->getId(),
        'publishing_status_id' => $publishingStatus->getId(),
    ]);
    $this->assertDatabaseHas('tour_best_months', ['tour_id' => $tour->getId(), 'month_id' => $june->getId()]);
    $this->assertDatabaseHas('tour_best_months', ['tour_id' => $tour->getId(), 'month_id' => $september->getId()]);
});
