<?php

declare(strict_types=1);

use App\Support\Testing\ProvidesTestingData;
use Tests\Integration\Admin\Product\ModelTestCase;

uses(ModelTestCase::class);

it('should raise unauthorized', function (): void {
    $product  = ProvidesTestingData::createProductRandomItem()->firstOrFail();
    $response = $this->jsonWithHeader('PUT', $this->url('admin/products/'.$product->getPublicId()), ['name' => 'Updated']);
    $response->assertUnauthorized();
});

it('should raise validation errors', function (): void {
    ProvidesTestingData::createRandomUserAndAuthorize([], [
        'permissions' => $this->getPermissions(['read', 'update']),
    ]);
    $product  = ProvidesTestingData::createProductRandomItem()->firstOrFail();
    $response = $this->jsonWithHeader(
        'PUT',
        $this->url('admin/products/'.$product->getPublicId()),
        [
            'name'       => str_repeat('a', 161),
            'gtin'       => '123456789',
            'pack_count' => 0,
        ],
    );

    $response->assertJsonValidationErrors(['name', 'gtin', 'pack_count']);
});

it('should raise forbidden', function (): void {
    ProvidesTestingData::createRandomUserAndAuthorize();
    $product = ProvidesTestingData::createProductRandomItem()->firstOrFail();

    $response = $this->jsonWithHeader(
        'PUT',
        $this->url('admin/products/'.$product->getPublicId()),
        ['name' => 'Updated Product'],
    );

    $response->assertForbiddenPermissions($this->getPermissions(['read']));
    $response->assertForbidden();
});

it('should reject a missing category', function (): void {
    ProvidesTestingData::createRandomUserAndAuthorize([], [
        'permissions' => $this->getPermissions(['read', 'update']),
    ]);
    $product = ProvidesTestingData::createProductRandomItem()->firstOrFail();

    $response = $this->jsonWithHeader(
        'PUT',
        $this->url('admin/products/'.$product->getPublicId()),
        ['category_id' => '01J00000000000000000000000'],
    );

    $response->assertJsonValidationErrors(['category_id']);
});

it('should raise not found', function (): void {
    ProvidesTestingData::createRandomUserAndAuthorize([], [
        'permissions' => $this->getPermissions(['read', 'update']),
    ]);
    $response = $this->jsonWithHeader('PUT', $this->url('admin/products/01J00000000000000000000000'), ['name' => 'Updated']);
    $response->assertNotFound();
});

it('should update a product', function (): void {
    ProvidesTestingData::createRandomUserAndAuthorize([], [
        'permissions' => $this->getPermissions(['read', 'update']),
    ]);
    $product  = ProvidesTestingData::createProductRandomItem()->firstOrFail();
    $response = $this->jsonWithHeader('PUT', $this->url('admin/products/'.$product->getPublicId()), ['name' => 'Updated']);
    $response->assertOk();
    $response->assertJsonPath('data.attributes.name', 'Updated');
});
