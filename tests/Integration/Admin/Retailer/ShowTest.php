<?php

declare(strict_types=1);

use App\Support\Testing\ProvidesTestingData;
use Tests\Integration\Admin\Retailer\ModelTestCase;

uses(ModelTestCase::class);

it('should raise unauthorized', function (): void {
    $response = $this->jsonWithHeader('GET', $this->url('admin/retailers/01J00000000000000000000000'));

    $response->assertUnauthorized();
});

it('should raise forbidden', function (): void {
    ProvidesTestingData::createRandomUserAndAuthorize();

    $response = $this->jsonWithHeader('GET', $this->url('admin/retailers/01J00000000000000000000000'));

    $response->assertForbiddenPermissions($this->getPermissions(['read']));
    $response->assertForbidden();
});

it('should raise not found', function (): void {
    ProvidesTestingData::createRandomUserAndAuthorize([], [
        'permissions' => $this->getPermissions(['read']),
    ]);

    $response = $this->jsonWithHeader('GET', $this->url('admin/retailers/01J00000000000000000000000'));

    $response->assertNotFound();
});

it('should show retailer', function (): void {
    ProvidesTestingData::createRandomUserAndAuthorize([], [
        'permissions' => $this->getPermissions(['read']),
    ]);
    $retailer = ProvidesTestingData::createRetailerRandomItem()->firstOrFail();

    $response = $this->jsonWithHeader(
        'GET',
        $this->url('admin/retailers/'.$retailer->getPublicId()),
    );

    $response->assertJsonDataItemStructure($this->getRetailerStructure());
});
