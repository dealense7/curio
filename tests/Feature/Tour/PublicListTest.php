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

it('should return only published tours and filter by lookup ids', function (): void {
    /** @var Difficulty $moderate */
    $moderate = ProvidesTestingData::createDifficultyRandomItem(['key' => DifficultyEnum::MODERATE])->first();
    /** @var PublishingStatus $publishedPublishingStatus */
    $publishedPublishingStatus = ProvidesTestingData::createPublishingStatusRandomItem(['key' => PublishingStatusEnum::PUBLISHED])->first();
    /** @var PublishingStatus $privatePublishingStatus */
    $privatePublishingStatus = ProvidesTestingData::createPublishingStatusRandomItem(['key' => PublishingStatusEnum::PRIVATE])->first();
    /** @var Month $june */
    $june = ProvidesTestingData::createMonthRandomItem(['key' => MonthEnum::JUNE, 'sort_order' => 6])->first();
    /** @var Month $january */
    $january = ProvidesTestingData::createMonthRandomItem(['key' => MonthEnum::JANUARY, 'sort_order' => 1])->first();
    /** @var Currency $currency */
    $currency = ProvidesTestingData::createCurrencyRandomItem()->first();

    /** @var Tour $matchingTour */
    $matchingTour = ProvidesTestingData::createTourRandomItem([
        'title'                => 'Summer Moderate Tour',
        'difficulty_id'        => $moderate->getId(),
        'publishing_status_id' => $publishedPublishingStatus->getId(),
        'currency_id'          => $currency->getId(),
    ])->first();
    $matchingTour->bestMonths()->attach($june->getId());

    /** @var Tour $winterTour */
    $winterTour = ProvidesTestingData::createTourRandomItem([
        'difficulty_id'        => $moderate->getId(),
        'publishing_status_id' => $publishedPublishingStatus->getId(),
        'currency_id'          => $currency->getId(),
    ])->first();
    $winterTour->bestMonths()->attach($january->getId());

    /** @var Tour $privatePublishingStatusTour */
    $privatePublishingStatusTour = ProvidesTestingData::createTourRandomItem([
        'difficulty_id'        => $moderate->getId(),
        'publishing_status_id' => $privatePublishingStatus->getId(),
        'currency_id'          => $currency->getId(),
    ])->first();
    $privatePublishingStatusTour->bestMonths()->attach($june->getId());

    $response = $this->jsonWithHeader('GET', $this->url('general/tours'), [
        'month_id'      => $june->getId(),
        'difficulty_id' => $moderate->getId(),
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

    $items        = collect($response->json('data'));
    $ids          = $items->pluck('id');
    $matchingItem = $items->firstWhere('id', $matchingTour->getPublicId());

    expect($ids)->toContain($matchingTour->getPublicId())
        ->not->toContain($winterTour->getPublicId(), $privatePublishingStatusTour->getPublicId())
        ->and(data_get($matchingItem, 'relationships.difficulty.data.id'))->toBe($moderate->getId())
        ->and(data_get($matchingItem, 'relationships.bestMonths.data.0.id'))->toBe($june->getId());
});

it('should validate public lookup filters', function (): void {
    $response = $this->jsonWithHeader('GET', $this->url('general/tours'), [
        'month_id'      => 999999,
        'difficulty_id' => 999999,
    ]);

    $response->assertJsonValidationErrors([
        'month_id',
        'difficulty_id',
    ]);
});
