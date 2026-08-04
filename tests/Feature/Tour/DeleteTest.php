<?php

declare(strict_types=1);

use App\Models\Tour\Tour;
use App\Support\Testing\ProvidesTestingData;
use Tests\Feature\Tour\ModelTestCase;

uses(ModelTestCase::class);

it('should raise unauthorized', function (): void {
    /** @var Tour $tour */
    $tour = ProvidesTestingData::createTourRandomItem()->first();

    $response = $this->jsonWithHeader('DELETE', $this->url('admin/tours/'.$tour->getPublicId()));

    $response->assertUnauthorized();
});

it('should raise forbidden', function (): void {
    /** @var Tour $tour */
    $tour = ProvidesTestingData::createTourRandomItem()->first();
    ProvidesTestingData::createRandomUserAndAuthorize([], ['permissions' => $this->getPermissions(['read'])]);

    $response = $this->jsonWithHeader('DELETE', $this->url('admin/tours/'.$tour->getPublicId()));

    $response->assertForbiddenPermissions($this->getPermissions(['delete']));
    $response->assertForbidden();
});

it('should raise not found', function (): void {
    ProvidesTestingData::createRandomUserAndAuthorize([], ['permissions' => $this->getPermissions(['read', 'delete'])]);

    $response = $this->jsonWithHeader('DELETE', $this->url('admin/tours/01J00000000000000000000000'));

    $response->assertNotFound();
});

it('should soft delete a tour', function (): void {
    /** @var Tour $tour */
    $tour = ProvidesTestingData::createTourRandomItem()->first();
    ProvidesTestingData::createRandomUserAndAuthorize([], ['permissions' => $this->getPermissions(['read', 'delete'])]);

    $response = $this->jsonWithHeader('DELETE', $this->url('admin/tours/'.$tour->getPublicId()));

    $response->assertOk();

    $this->assertSoftDeleted($tour->getTable(), ['id' => $tour->getId()]);
});
