<?php

declare(strict_types=1);

use App\Enums\General\Month as MonthEnum;
use App\Models\General\Month;
use App\Models\Tour\Tour;
use App\Support\Testing\ProvidesTestingData;
use Tests\Feature\Tour\ModelTestCase;

uses(ModelTestCase::class);

it('should raise unauthorized', function (): void {
    /** @var Tour $tour */
    $tour = ProvidesTestingData::createTourRandomItem()->first();

    $response = $this->jsonWithHeader('GET', $this->url('admin/tours/'.$tour->getPublicId()));

    $response->assertUnauthorized();
});

it('should raise forbidden', function (): void {
    /** @var Tour $tour */
    $tour = ProvidesTestingData::createTourRandomItem()->first();
    ProvidesTestingData::createRandomUserAndAuthorize();

    $response = $this->jsonWithHeader('GET', $this->url('admin/tours/'.$tour->getPublicId()));

    $response->assertForbiddenPermissions($this->getPermissions(['read']));
    $response->assertForbidden();
});

it('should raise not found', function (): void {
    ProvidesTestingData::createRandomUserAndAuthorize([], ['permissions' => $this->getPermissions(['read'])]);

    $response = $this->jsonWithHeader('GET', $this->url('admin/tours/01J00000000000000000000000'));

    $response->assertNotFound();
});

it('should show a tour with transformed relations', function (): void {
    /** @var Tour $tour */
    $tour      = ProvidesTestingData::createTourRandomItem()->first();
    /** @var Month $june */
    $june      = ProvidesTestingData::createMonthRandomItem(['key' => MonthEnum::JUNE, 'sort_order' => 6])->first();
    /** @var Month $september */
    $september = ProvidesTestingData::createMonthRandomItem(['key' => MonthEnum::SEPTEMBER, 'sort_order' => 9])->first();
    $tour->bestMonths()->attach([$june->getId(), $september->getId()]);
    ProvidesTestingData::createRandomUserAndAuthorize([], ['permissions' => $this->getPermissions(['read'])]);

    $response = $this->jsonWithHeader('GET', $this->url('admin/tours/'.$tour->getPublicId()));

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
