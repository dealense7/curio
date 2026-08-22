<?php

declare(strict_types=1);

use App\Support\Testing\ProvidesTestingData;
use Tests\Integration\Admin\Category\ModelTestCase;

uses(ModelTestCase::class);

it('should raise unauthorized', function (): void {
    $response = $this->jsonWithHeader('DELETE', $this->url('admin/categories/01J00000000000000000000000'));

    $response->assertUnauthorized();
});

it('should raise forbidden', function (): void {
    ProvidesTestingData::createRandomUserAndAuthorize();
    $category = ProvidesTestingData::createCategoryRandomItem()->firstOrFail();

    $response = $this->jsonWithHeader('DELETE', $this->url('admin/categories/'.$category->getPublicId()));

    $response->assertForbiddenPermissions($this->getPermissions(['read']));
    $response->assertForbidden();
});

it('should raise not found', function (): void {
    ProvidesTestingData::createRandomUserAndAuthorize([], [
        'permissions' => $this->getPermissions(['delete']),
    ]);

    $response = $this->jsonWithHeader('DELETE', $this->url('admin/categories/01J00000000000000000000000'));

    $response->assertNotFound();
});

it('should delete a category', function (): void {
    ProvidesTestingData::createRandomUserAndAuthorize([], [
        'permissions' => $this->getPermissions(['read', 'delete']),
    ]);
    $category = ProvidesTestingData::createCategoryRandomItem()->firstOrFail();

    $response = $this->jsonWithHeader('DELETE', $this->url('admin/categories/'.$category->getPublicId()));

    $response->assertOk();
    $this->assertSoftDeleted('categories', ['id' => $category->getId()]);
});
