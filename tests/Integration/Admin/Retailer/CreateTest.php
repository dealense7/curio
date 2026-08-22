<?php

declare(strict_types=1);

use App\Support\Testing\ProvidesTestingData;
use Tests\Integration\Admin\Retailer\ModelTestCase;

uses(ModelTestCase::class);

it('should raise unauthorized', function (): void {
    $response = $this->jsonWithHeader('POST', $this->url('admin/retailers'));

    $response->assertUnauthorized();
});

it('should raise validation errors', function (): void {
    ProvidesTestingData::createRandomUserAndAuthorize();

    $response = $this->jsonWithHeader('POST', $this->url('admin/retailers'));

    $response->assertJsonValidationErrors([
        'name',
        'slug',
        'country_id',
        'currency_id',
    ]);
});

it('should raise validation errors for missing references', function (): void {
    ProvidesTestingData::createRandomUserAndAuthorize([], [
        'permissions' => $this->getPermissions(['create']),
    ]);
    $data               = $this->getRetailerData();
    $data['country_id'] = str()->ulid();

    $response = $this->jsonWithHeader('POST', $this->url('admin/retailers'), $data);

    $response->assertJsonValidationErrors(['country_id']);
});

it('should validate retailer field rules', function (): void {
    ProvidesTestingData::createRandomUserAndAuthorize([], [
        'permissions' => $this->getPermissions(['create']),
    ]);
    $data                     = $this->getRetailerData();
    $data['name']             = str_repeat('a', 161);
    $data['slug']             = 'Invalid Slug';
    $data['domain']           = str_repeat('a', 256);
    $data['country_id']       = 'not-a-ulid';
    $data['currency_id']      = 'not-a-ulid';
    $data['is_active']        = 'yes';
    $data['scraping_enabled'] = 'yes';
    $data['last_scraped_at']  = 'not-a-date';

    $response = $this->jsonWithHeader('POST', $this->url('admin/retailers'), $data);

    $response->assertJsonValidationErrors([
        'name',
        'slug',
        'domain',
        'country_id',
        'currency_id',
        'is_active',
        'scraping_enabled',
        'last_scraped_at',
    ]);
});

it('should reject a duplicate retailer slug', function (): void {
    ProvidesTestingData::createRandomUserAndAuthorize([], [
        'permissions' => $this->getPermissions(['create']),
    ]);
    ProvidesTestingData::createRetailerRandomItem(['slug' => 'existing-retailer']);
    $data         = $this->getRetailerData();
    $data['slug'] = 'existing-retailer';

    $response = $this->jsonWithHeader('POST', $this->url('admin/retailers'), $data);

    $response->assertJsonValidationErrors(['slug']);
});

it('should raise forbidden', function (): void {
    ProvidesTestingData::createRandomUserAndAuthorize();

    $response = $this->jsonWithHeader(
        'POST',
        $this->url('admin/retailers'),
        $this->getRetailerData(),
    );

    $response->assertForbiddenPermissions($this->getPermissions(['create']));
    $response->assertForbidden();
});

it('should create retailer', function (): void {
    $user = ProvidesTestingData::createRandomUserAndAuthorize([], [
        'permissions' => $this->getPermissions(['create']),
    ]);

    $data           = $this->getRetailerData();
    $data['slug']   = 'Example-Retailer';
    $data['domain'] = 'EXAMPLE.COM';

    $response = $this->jsonWithHeader(
        'POST',
        $this->url('admin/retailers'),
        $data,
    );

    $response->assertCreated()
        ->assertJsonDataItemStructure($this->getRetailerStructure());

    $this->assertDatabaseHas('retailers', [
        'public_id'  => $response->json('data.id'),
        'slug'       => 'example-retailer',
        'domain'     => 'example.com',
        'created_by' => $user->getId(),
        'updated_by' => $user->getId(),
    ]);
});
