<?php

declare(strict_types=1);

use App\Support\Testing\ProvidesTestingData;
use Tests\Integration\Admin\Product\ModelTestCase;

uses(ModelTestCase::class);

it('should raise unauthorized', function (): void {
    $response = $this->jsonWithHeader('POST', $this->url('admin/products'), $this->getProductData());
    $response->assertUnauthorized();
});

it('should raise validation errors', function (): void {
    ProvidesTestingData::createRandomUserAndAuthorize([], ['permissions' => $this->getPermissions(['create'])]);
    $response = $this->jsonWithHeader('POST', $this->url('admin/products'), [
        'name'       => str_repeat('a', 161),
        'gtin'       => '123456789',
        'pack_count' => 0,
    ]);

    $response->assertJsonValidationErrors(['name', 'gtin', 'pack_count']);
});

it('should raise forbidden', function (): void {
    ProvidesTestingData::createRandomUserAndAuthorize();

    $response = $this->jsonWithHeader('POST', $this->url('admin/products'), $this->getProductData());

    $response->assertForbiddenPermissions($this->getPermissions(['create']));
    $response->assertForbidden();
});

it('should reject a missing category', function (): void {
    ProvidesTestingData::createRandomUserAndAuthorize([], [
        'permissions' => $this->getPermissions(['create']),
    ]);

    $data                = $this->getProductData();
    $data['category_id'] = '01J00000000000000000000000';

    $response = $this->jsonWithHeader('POST', $this->url('admin/products'), $data);

    $response->assertJsonValidationErrors(['category_id']);
});

it('should create a product', function (): void {
    ProvidesTestingData::createRandomUserAndAuthorize([], ['permissions' => $this->getPermissions(['create'])]);
    $response = $this->jsonWithHeader('POST', $this->url('admin/products'), $this->getProductData());
    $response->assertCreated();
    $response->assertJsonDataItemStructure($this->getProductStructure());
});
