<?php

declare(strict_types=1);

use App\Enums\General\Difficulty as DifficultyEnum;
use App\Enums\General\FileType;
use App\Enums\General\Month as MonthEnum;
use App\Models\General\Currency;
use App\Models\General\Difficulty;
use App\Models\General\File;
use App\Models\General\Month;
use App\Models\General\PublishingStatus;
use App\Models\Tour\Tour;
use App\Support\Testing\ProvidesTestingData;
use Tests\Feature\Tour\ModelTestCase;

uses(ModelTestCase::class);

it('should raise unauthorized', function (): void {
    /** @var Tour $tour */
    $tour = ProvidesTestingData::createTourRandomItem()->first();

    $response = $this->jsonWithHeader('PUT', $this->url('admin/tours/'.$tour->getPublicId()));

    $response->assertUnauthorized();
});

it('should raise validation errors', function (): void {
    /** @var Tour $tour */
    $tour = ProvidesTestingData::createTourRandomItem()->first();
    ProvidesTestingData::createRandomUserAndAuthorize([], ['permissions' => $this->getPermissions(['read', 'update'])]);

    $response = $this->jsonWithHeader('PUT', $this->url('admin/tours/'.$tour->getPublicId()));

    $response->assertJsonValidationErrors([
        'title',
        'duration_comfortable_days',
        'duration_recommended_days',
        'daily_distance_comfortable_km',
        'daily_distance_recommended_km',
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

it('should raise forbidden', function (): void {
    /** @var Tour $tour */
    $tour = ProvidesTestingData::createTourRandomItem()->first();
    ProvidesTestingData::createRandomUserAndAuthorize([], ['permissions' => $this->getPermissions(['read'])]);
    $difficulty           = $tour->difficulty;
    $currency             = $tour->currency;
    $publishingStatus     = $tour->publishingStatus;
    /** @var Month $month */
    $month                = ProvidesTestingData::createMonthRandomItem()->first();
    /** @var File $route */
    $route                = ProvidesTestingData::createFileRandomItem(['type' => FileType::ROUTE])->first();
    /** @var File $cover */
    $cover                = ProvidesTestingData::createFileRandomItem(['type' => FileType::IMAGE])->first();

    $response = $this->jsonWithHeader('PUT', $this->url('admin/tours/'.$tour->getPublicId()), [
        'title'                         => 'Forbidden Update',
        'description'                   => 'Valid data without update permission.',
        'start_location'                => 'Start',
        'end_location'                  => 'End',
        'distance_km'                   => 20,
        'duration_comfortable_days'     => 2,
        'duration_recommended_days'     => 1,
        'daily_distance_comfortable_km' => 50,
        'daily_distance_recommended_km' => 100,
        'elevation_gain_m'              => 500,
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

    $response->assertForbiddenPermissions($this->getPermissions(['update']));
    $response->assertForbidden();
});

it('should raise not found', function (): void {
    ProvidesTestingData::createRandomUserAndAuthorize([], ['permissions' => $this->getPermissions(['read', 'update'])]);
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

    $response = $this->jsonWithHeader('PUT', $this->url('admin/tours/01J00000000000000000000000'), [
        'title'                         => 'Missing Tour',
        'description'                   => 'The Tour public ID does not exist.',
        'start_location'                => 'Start',
        'end_location'                  => 'End',
        'distance_km'                   => 20,
        'duration_comfortable_days'     => 2,
        'duration_recommended_days'     => 1,
        'daily_distance_comfortable_km' => 50,
        'daily_distance_recommended_km' => 100,
        'elevation_gain_m'              => 500,
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

    $response->assertNotFound();
});

it('should update a tour and synchronize its relations', function (): void {
    /** @var Tour $tour */
    $tour = ProvidesTestingData::createTourRandomItem()->first();
    /** @var Month $june */
    $june = ProvidesTestingData::createMonthRandomItem(['key' => MonthEnum::JUNE, 'sort_order' => 6])->first();
    $tour->bestMonths()->attach($june->getId());
    ProvidesTestingData::createRandomUserAndAuthorize([], ['permissions' => $this->getPermissions(['read', 'update'])]);

    /** @var Difficulty $difficulty */
    $difficulty           = ProvidesTestingData::createDifficultyRandomItem(['key' => DifficultyEnum::DIFFICULT])->first();
    $currency             = $tour->currency;
    $publishingStatus     = $tour->publishingStatus;
    /** @var Month $july */
    $july                 = ProvidesTestingData::createMonthRandomItem(['key' => MonthEnum::JULY, 'sort_order' => 7])->first();
    /** @var File $route */
    $route                = ProvidesTestingData::createFileRandomItem(['type' => FileType::ROUTE])->first();
    /** @var File $cover */
    $cover                = ProvidesTestingData::createFileRandomItem(['type' => FileType::IMAGE])->first();

    $response = $this->jsonWithHeader('PUT', $this->url('admin/tours/'.$tour->getPublicId()), [
        'title'                         => 'Updated Kazbegi Traverse',
        'description'                   => 'Updated route details.',
        'start_location'                => 'New Start',
        'end_location'                  => 'New End',
        'distance_km'                   => '55.25',
        'duration_comfortable_days'     => 5,
        'duration_recommended_days'     => 4,
        'daily_distance_comfortable_km' => 80,
        'daily_distance_recommended_km' => 110,
        'elevation_gain_m'              => 2200,
        'difficulty_id'                 => $difficulty->getId(),
        'price_comfortable'             => 1400,
        'price_recommended'             => 2000,
        'average_daily_spend'           => 95,
        'currency_id'                   => $currency->getId(),
        'best_month_ids'                => [$july->getId()],
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
    $this->assertDatabaseHas($tour->getTable(), [
        'id'                   => $tour->getId(),
        'title'                => 'Updated Kazbegi Traverse',
        'difficulty_id'        => $difficulty->getId(),
        'currency_id'          => $currency->getId(),
        'route_file_id'        => $route->getId(),
        'cover_image_id'       => $cover->getId(),
        'publishing_status_id' => $publishingStatus->getId(),
    ]);
    $this->assertDatabaseHas('tour_best_months', ['tour_id' => $tour->getId(), 'month_id' => $july->getId()]);
    $this->assertDatabaseMissing('tour_best_months', ['tour_id' => $tour->getId(), 'month_id' => $june->getId()]);
});
