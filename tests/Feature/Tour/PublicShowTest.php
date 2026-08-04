<?php

declare(strict_types=1);

use App\Enums\General\Month as MonthEnum;
use App\Enums\General\PublishingStatus as PublishingStatusEnum;
use App\Models\General\Month;
use App\Models\General\PublishingStatus;
use App\Models\Tour\Tour;
use App\Support\Testing\ProvidesTestingData;
use Tests\Feature\Tour\ModelTestCase;

uses(ModelTestCase::class);

it('should show a published tour', function (): void {
    /** @var PublishingStatus $publishedPublishingStatus */
    $publishedPublishingStatus = ProvidesTestingData::createPublishingStatusRandomItem(['key' => PublishingStatusEnum::PUBLISHED])->first();
    /** @var Month $july */
    $july                      = ProvidesTestingData::createMonthRandomItem(['key' => MonthEnum::JULY, 'sort_order' => 7])->first();
    /** @var Tour $tour */
    $tour                      = ProvidesTestingData::createTourRandomItem(['publishing_status_id' => $publishedPublishingStatus->getId()])->first();
    $tour->bestMonths()->attach($july->getId());

    $response = $this->jsonWithHeader('GET', $this->url('general/tours/'.$tour->getPublicId()));

    $response->assertOk();
    $response->assertJsonDataItemStructure($this->getTourStructure([
        'difficulty',
        'currency',
        'publishingStatus',
        'routeFile:file',
        'coverImage:file',
        '[bestMonths:month]',
    ]));
});

it('should not expose a private tour', function (): void {
    /** @var PublishingStatus $privatePublishingStatus */
    $privatePublishingStatus = ProvidesTestingData::createPublishingStatusRandomItem(['key' => PublishingStatusEnum::PRIVATE])->first();
    /** @var Tour $tour */
    $tour                    = ProvidesTestingData::createTourRandomItem(['publishing_status_id' => $privatePublishingStatus->getId()])->first();

    $response = $this->jsonWithHeader('GET', $this->url('general/tours/'.$tour->getPublicId()));

    $response->assertNotFound();
});

it('should raise not found for an unknown tour', function (): void {
    $response = $this->jsonWithHeader('GET', $this->url('general/tours/01J00000000000000000000000'));

    $response->assertNotFound();
});
