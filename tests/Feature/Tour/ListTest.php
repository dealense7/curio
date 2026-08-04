<?php

declare(strict_types=1);

use App\Enums\General\Difficulty as DifficultyEnum;
use App\Enums\General\Month as MonthEnum;
use App\Enums\General\PublishingStatus as PublishingStatusEnum;
use App\Models\General\Currency;
use App\Models\General\Difficulty;
use App\Models\General\Month;
use App\Models\General\PublishingStatus;
use App\Models\Tour\Tour;
use App\Support\Testing\ProvidesTestingData;
use Tests\Feature\Tour\ModelTestCase;

uses(ModelTestCase::class);

it('should raise unauthorized', function (): void {
    $response = $this->jsonWithHeader('GET', $this->url('admin/tours'));

    $response->assertUnauthorized();
});

it('should raise forbidden', function (): void {
    ProvidesTestingData::createRandomUserAndAuthorize();

    $response = $this->jsonWithHeader('GET', $this->url('admin/tours'));

    $response->assertForbiddenPermissions($this->getPermissions(['read']));
    $response->assertForbidden();
});

it('should return and filter admin tours by lookup ids', function (): void {
    /** @var Difficulty $difficulty */
    $difficulty = ProvidesTestingData::createDifficultyRandomItem(['key' => DifficultyEnum::MODERATE])->first();
    /** @var Difficulty $otherDifficulty */
    $otherDifficulty = ProvidesTestingData::createDifficultyRandomItem(['key' => DifficultyEnum::DIFFICULT])->first();
    /** @var PublishingStatus $privatePublishingStatus */
    $privatePublishingStatus = ProvidesTestingData::createPublishingStatusRandomItem(['key' => PublishingStatusEnum::PRIVATE])->first();
    /** @var PublishingStatus $publishedPublishingStatus */
    $publishedPublishingStatus = ProvidesTestingData::createPublishingStatusRandomItem(['key' => PublishingStatusEnum::PUBLISHED])->first();
    /** @var Month $june */
    $june = ProvidesTestingData::createMonthRandomItem(['key' => MonthEnum::JUNE, 'sort_order' => 6])->first();
    /** @var Month $january */
    $january = ProvidesTestingData::createMonthRandomItem(['key' => MonthEnum::JANUARY, 'sort_order' => 1])->first();
    /** @var Currency $currency */
    $currency = ProvidesTestingData::createCurrencyRandomItem()->first();

    /** @var Tour $matchingTour */
    $matchingTour = ProvidesTestingData::createTourRandomItem([
        'title'                => 'Matching Private Tour',
        'difficulty_id'        => $difficulty->getId(),
        'publishing_status_id' => $privatePublishingStatus->getId(),
        'currency_id'          => $currency->getId(),
    ])->first();
    $matchingTour->bestMonths()->attach($june->getId());

    /** @var Tour $wrongMonth */
    $wrongMonth = ProvidesTestingData::createTourRandomItem([
        'difficulty_id'        => $difficulty->getId(),
        'publishing_status_id' => $privatePublishingStatus->getId(),
        'currency_id'          => $currency->getId(),
    ])->first();
    $wrongMonth->bestMonths()->attach($january->getId());

    /** @var Tour $wrongValues */
    $wrongValues = ProvidesTestingData::createTourRandomItem([
        'difficulty_id'        => $otherDifficulty->getId(),
        'publishing_status_id' => $publishedPublishingStatus->getId(),
        'currency_id'          => $currency->getId(),
    ])->first();
    $wrongValues->bestMonths()->attach($june->getId());

    ProvidesTestingData::createRandomUserAndAuthorize([], ['permissions' => $this->getPermissions(['read'])]);

    $response = $this->jsonWithHeader('GET', $this->url('admin/tours'), [
        'month_id'             => $june->getId(),
        'difficulty_id'        => $difficulty->getId(),
        'publishing_status_id' => $privatePublishingStatus->getId(),
    ]);

    $response->assertOk();
    $response->assertJsonDataCollectionStructure($this->getTourStructure([
        'difficulty',
        'currency',
        'publishingStatus',
        'routeFile:file',
        'coverImage:file',
        '[bestMonths:month]',
    ]), false);
    $response->assertJsonCount(1, 'data');
    $response->assertJsonPath('data.0.id', $matchingTour->getPublicId());
});
