<?php

declare(strict_types=1);

use App\Support\Testing\ProvidesTestingData;
use Tests\Integration\Admin\Category\ModelTestCase;

uses(ModelTestCase::class);

it('should raise unauthorized', function (): void {
    $response = $this->jsonWithHeader('GET', $this->url('admin/categories'));

    $response->assertUnauthorized();
});

it('should raise forbidden', function (): void {
    ProvidesTestingData::createRandomUserAndAuthorize();

    $response = $this->jsonWithHeader('GET', $this->url('admin/categories'));

    $response->assertForbiddenPermissions($this->getPermissions(['read']));
    $response->assertForbidden();
});

it('should return filtered categories', function (): void {
    ProvidesTestingData::createRandomUserAndAuthorize([], [
        'permissions' => $this->getPermissions(['read']),
    ]);
    ProvidesTestingData::createCategoryRandomItem(['name' => 'Food']);
    ProvidesTestingData::createCategoryRandomItem(['name' => 'Drinks']);

    $data = [
        'filters' => ['name' => 'Food'],
        'sort'    => 'name',
    ];
    $response = $this->jsonWithHeader('GET', $this->url('admin/categories'), $data);

    $response->assertJsonDataCount(1);
    $response->assertJsonPath('data.0.attributes.name', 'Food');
    $response->assertJsonDataCollectionStructure($this->getCategoryStructure());
});

it('should return no categories for an unknown parent filter', function (): void {
    ProvidesTestingData::createRandomUserAndAuthorize([], [
        'permissions' => $this->getPermissions(['read']),
    ]);
    ProvidesTestingData::createCategoryRandomItem();

    $data = [
        'filters' => ['parent_id' => (string) str()->ulid()],
    ];
    $response = $this->jsonWithHeader('GET', $this->url('admin/categories'), $data);

    $response->assertJsonDataCount(0);
});
