<?php

declare(strict_types=1);

use App\Support\Testing\ProvidesTestingData;
use Tests\Integration\Admin\Retailer\ModelTestCase;

uses(ModelTestCase::class);

it('should raise unauthorized', function (): void {
    $response = $this->jsonWithHeader('GET', $this->url('admin/retailers'));

    $response->assertUnauthorized();
});

it('should raise forbidden', function (): void {
    ProvidesTestingData::createRandomUserAndAuthorize();

    $response = $this->jsonWithHeader('GET', $this->url('admin/retailers'));

    $response->assertForbiddenPermissions($this->getPermissions(['read']));
    $response->assertForbidden();
});

it('should return filtered retailers', function (): void {
    ProvidesTestingData::createRandomUserAndAuthorize([], [
        'permissions' => $this->getPermissions(['read']),
    ]);
    ProvidesTestingData::createRetailerRandomItem(['name' => 'Zeta Retailer']);
    ProvidesTestingData::createRetailerRandomItem([
        'name'      => 'Alpha Retailer',
        'is_active' => false,
    ]);

    $data = [
        'filters' => ['is_active' => true],
        'sort'    => 'name',
    ];
    $response = $this->jsonWithHeader('GET', $this->url('admin/retailers'), $data);

    $response->assertJsonDataCount(1);
    $response->assertJsonPath('data.0.attributes.name', 'Zeta Retailer');
    $response->assertJsonDataPagination([
        'page'    => 1,
        'perPage' => 15,
        'count'   => 1,
        'total'   => 1,
    ]);
    $response->assertJsonDataCollectionStructure($this->getRetailerStructure());
});
