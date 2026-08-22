<?php

declare(strict_types=1);

use App\Support\Testing\ProvidesTestingData;
use Tests\Integration\Admin\Category\ModelTestCase;

uses(ModelTestCase::class);

it('should raise unauthorized', function (): void {
    $category = ProvidesTestingData::createCategoryRandomItem()->firstOrFail();

    $response = $this->jsonWithHeader('PUT', $this->url('admin/categories/'.$category->getPublicId()), ['name' => 'Updated']);

    $response->assertUnauthorized();
});

it('should raise validation errors', function (): void {
    ProvidesTestingData::createRandomUserAndAuthorize([], [
        'permissions' => $this->getPermissions(['read', 'update']),
    ]);
    $category = ProvidesTestingData::createCategoryRandomItem()->firstOrFail();

    $data = [
        'name' => str_repeat('a', 161),
        'slug' => 'Invalid Slug',
    ];
    $response = $this->jsonWithHeader(
        'PUT',
        $this->url('admin/categories/'.$category->getPublicId()),
        $data,
    );

    $response->assertJsonValidationErrors(['name', 'slug']);
});

it('should reject a duplicate slug', function (): void {
    ProvidesTestingData::createRandomUserAndAuthorize([], [
        'permissions' => $this->getPermissions(['read', 'update']),
    ]);
    ProvidesTestingData::createCategoryRandomItem(['slug' => 'existing']);
    $category = ProvidesTestingData::createCategoryRandomItem()->firstOrFail();

    $response = $this->jsonWithHeader(
        'PUT',
        $this->url('admin/categories/'.$category->getPublicId()),
        ['slug' => 'existing'],
    );

    $response->assertJsonValidationErrors(['slug']);
});

it('should raise forbidden', function (): void {
    ProvidesTestingData::createRandomUserAndAuthorize();
    $category = ProvidesTestingData::createCategoryRandomItem()->firstOrFail();

    $response = $this->jsonWithHeader(
        'PUT',
        $this->url('admin/categories/'.$category->getPublicId()),
        ['name' => 'Updated Category'],
    );

    $response->assertForbiddenPermissions($this->getPermissions(['read']));
    $response->assertForbidden();
});

it('should raise not found', function (): void {
    ProvidesTestingData::createRandomUserAndAuthorize([], [
        'permissions' => $this->getPermissions(['update']),
    ]);

    $response = $this->jsonWithHeader(
        'PUT',
        $this->url('admin/categories/01J00000000000000000000000'),
        ['name' => 'Updated Category'],
    );

    $response->assertNotFound();
});

it('should update a category', function (): void {
    ProvidesTestingData::createRandomUserAndAuthorize([], [
        'permissions' => $this->getPermissions(['read', 'update']),
    ]);
    $category = ProvidesTestingData::createCategoryRandomItem()->firstOrFail();

    $response = $this->jsonWithHeader(
        'PUT',
        $this->url('admin/categories/'.$category->getPublicId()),
        ['name' => 'Updated Category'],
    );

    $response->assertJsonPath('data.attributes.name', 'Updated Category');
});
