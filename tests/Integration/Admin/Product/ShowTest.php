<?php

declare(strict_types=1);

use App\Support\Testing\ProvidesTestingData;
use Tests\Integration\Admin\Product\ModelTestCase;

uses(ModelTestCase::class);

it('should raise unauthorized', function (): void {
    $response = $this->jsonWithHeader('GET', $this->url('admin/products/01J00000000000000000000000'));
    $response->assertUnauthorized();
});

it('should raise forbidden', function (): void {
    ProvidesTestingData::createRandomUserAndAuthorize();
    $product  = ProvidesTestingData::createProductRandomItem()->firstOrFail();
    $response = $this->jsonWithHeader('GET', $this->url('admin/products/'.$product->getPublicId()));
    $response->assertForbiddenPermissions($this->getPermissions(['read']));
    $response->assertForbidden();
});

it('should raise not found', function (): void {
    ProvidesTestingData::createRandomUserAndAuthorize([], ['permissions' => $this->getPermissions(['read'])]);
    $response = $this->jsonWithHeader('GET', $this->url('admin/products/01J00000000000000000000000'));
    $response->assertNotFound();
});

it('should show a product', function (): void {
    ProvidesTestingData::createRandomUserAndAuthorize([], ['permissions' => $this->getPermissions(['read'])]);
    $product  = ProvidesTestingData::createProductRandomItem()->firstOrFail();
    $response = $this->jsonWithHeader('GET', $this->url('admin/products/'.$product->getPublicId()));
    $response->assertOk();
    $response->assertJsonDataItemStructure($this->getProductStructure());
});
