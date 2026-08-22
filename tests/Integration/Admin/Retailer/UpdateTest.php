<?php

declare(strict_types=1);

use App\Support\Testing\ProvidesTestingData;
use Tests\Integration\Admin\Retailer\ModelTestCase;

uses(ModelTestCase::class);

it('should raise unauthorized', function (): void {
    $response = $this->jsonWithHeader('PUT', $this->url('admin/retailers/01J00000000000000000000000'));

    $response->assertUnauthorized();
});

it('should raise validation errors', function (): void {
    ProvidesTestingData::createRandomUserAndAuthorize([], [
        'permissions' => $this->getPermissions(['update']),
    ]);
    $retailer = ProvidesTestingData::createRetailerRandomItem()->firstOrFail();

    $response = $this->jsonWithHeader(
        'PUT',
        $this->url('admin/retailers/'.$retailer->getPublicId()),
        ['slug' => 'Invalid Slug'],
    );

    $response->assertJsonValidationErrors(['slug']);
});

it('should reject a duplicate retailer slug on update', function (): void {
    ProvidesTestingData::createRandomUserAndAuthorize([], [
        'permissions' => $this->getPermissions(['read', 'update']),
    ]);
    ProvidesTestingData::createRetailerRandomItem(['slug' => 'existing-retailer']);
    $retailer = ProvidesTestingData::createRetailerRandomItem()->firstOrFail();

    $response = $this->jsonWithHeader(
        'PUT',
        $this->url('admin/retailers/'.$retailer->getPublicId()),
        ['slug' => 'existing-retailer'],
    );

    $response->assertJsonValidationErrors(['slug']);
});

it('should validate update retailer field rules', function (): void {
    ProvidesTestingData::createRandomUserAndAuthorize([], [
        'permissions' => $this->getPermissions(['read', 'update']),
    ]);
    $retailer = ProvidesTestingData::createRetailerRandomItem()->firstOrFail();
    $data     = [
        'name'             => str_repeat('a', 161),
        'domain'           => str_repeat('a', 256),
        'country_id'       => 'not-a-ulid',
        'currency_id'      => 'not-a-ulid',
        'is_active'        => 'yes',
        'scraping_enabled' => 'yes',
        'last_scraped_at'  => 'not-a-date',
    ];

    $response = $this->jsonWithHeader(
        'PUT',
        $this->url('admin/retailers/'.$retailer->getPublicId()),
        $data,
    );

    $response->assertJsonValidationErrors([
        'name',
        'domain',
        'country_id',
        'currency_id',
        'is_active',
        'scraping_enabled',
        'last_scraped_at',
    ]);
});

it('should raise forbidden', function (): void {
    ProvidesTestingData::createRandomUserAndAuthorize();
    $retailer = ProvidesTestingData::createRetailerRandomItem()->firstOrFail();

    $response = $this->jsonWithHeader(
        'PUT',
        $this->url('admin/retailers/'.$retailer->getPublicId()),
        ['name' => 'Updated Retailer'],
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
        $this->url('admin/retailers/01J00000000000000000000000'),
        ['name' => 'Updated Retailer'],
    );

    $response->assertNotFound();
});

it('should update retailer', function (): void {
    ProvidesTestingData::createRandomUserAndAuthorize([], [
        'permissions' => $this->getPermissions(['read', 'update']),
    ]);
    $retailer = ProvidesTestingData::createRetailerRandomItem()->firstOrFail();

    $response = $this->jsonWithHeader(
        'PUT',
        $this->url('admin/retailers/'.$retailer->getPublicId()),
        ['name' => 'Updated Retailer'],
    );

    $response->assertJsonPath('data.attributes.name', 'Updated Retailer');
});
