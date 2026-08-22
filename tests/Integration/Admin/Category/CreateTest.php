<?php

declare(strict_types=1);

use App\Support\Testing\ProvidesTestingData;
use Tests\Integration\Admin\Category\ModelTestCase;

uses(ModelTestCase::class);

it('should raise unauthorized', function (): void {
    $response = $this->jsonWithHeader('POST', $this->url('admin/categories'), $this->getCategoryData());

    $response->assertUnauthorized();
});

it('should raise validation errors', function (): void {
    ProvidesTestingData::createRandomUserAndAuthorize([], [
        'permissions' => $this->getPermissions(['create']),
    ]);

    $response = $this->jsonWithHeader('POST', $this->url('admin/categories'));

    $response->assertJsonValidationErrors(['name', 'slug']);
});

it('should reject a duplicate slug', function (): void {
    ProvidesTestingData::createRandomUserAndAuthorize([], [
        'permissions' => $this->getPermissions(['create']),
    ]);
    ProvidesTestingData::createCategoryRandomItem(['slug' => 'food']);

    $response = $this->jsonWithHeader('POST', $this->url('admin/categories'), $this->getCategoryData());

    $response->assertJsonValidationErrors(['slug']);
});

it('should raise forbidden', function (): void {
    ProvidesTestingData::createRandomUserAndAuthorize();

    $response = $this->jsonWithHeader('POST', $this->url('admin/categories'), $this->getCategoryData());

    $response->assertForbiddenPermissions($this->getPermissions(['create']));
    $response->assertForbidden();
});

it('should create a category', function (): void {
    ProvidesTestingData::createRandomUserAndAuthorize([], [
        'permissions' => $this->getPermissions(['create']),
    ]);

    $response = $this->jsonWithHeader('POST', $this->url('admin/categories'), $this->getCategoryData());

    $response->assertCreated()
        ->assertJsonDataItemStructure($this->getCategoryStructure());
});
