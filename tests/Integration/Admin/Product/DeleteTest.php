<?php

declare(strict_types=1);

use App\Models\Product\Product;
use App\Support\Testing\ProvidesTestingData;
use Tests\Integration\Admin\Product\ModelTestCase;

uses(ModelTestCase::class);

it('should raise unauthorized', function (): void {
    $product  = ProvidesTestingData::createProductRandomItem()->firstOrFail();
    $response = $this->jsonWithHeader('DELETE', $this->url('admin/products/'.$product->getPublicId()));
    $response->assertUnauthorized();
});

it('should raise forbidden', function (): void {
    ProvidesTestingData::createRandomUserAndAuthorize();
    $product = ProvidesTestingData::createProductRandomItem()->firstOrFail();

    $response = $this->jsonWithHeader('DELETE', $this->url('admin/products/'.$product->getPublicId()));

    $response->assertForbiddenPermissions($this->getPermissions(['read']));
    $response->assertForbidden();
});

it('should raise not found', function (): void {
    ProvidesTestingData::createRandomUserAndAuthorize([], [
        'permissions' => $this->getPermissions(['read', 'delete']),
    ]);

    $response = $this->jsonWithHeader('DELETE', $this->url('admin/products/01J00000000000000000000000'));

    $response->assertNotFound();
});

it('should delete a product', function (): void {
    ProvidesTestingData::createRandomUserAndAuthorize([], [
        'permissions' => $this->getPermissions(['read', 'delete']),
    ]);
    $product  = ProvidesTestingData::createProductRandomItem()->firstOrFail();
    $response = $this->jsonWithHeader('DELETE', $this->url('admin/products/'.$product->getPublicId()));
    $response->assertOk();
    expect(Product::withTrashed()->find($product->getId())->trashed())->toBeTrue();
});
