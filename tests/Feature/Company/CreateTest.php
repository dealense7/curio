<?php

declare(strict_types=1);

use App\Models\Company;
use App\Models\General\Country\Country;
use App\Models\General\Currency;
use App\Support\Testing\ProvidesTestingData;
use Tests\Feature\Company\ModelTestCase;

uses(ModelTestCase::class);

it('should raise unauthorized', function (): void {
    $response = $this->jsonWithHeader('POST', $this->url('admin/companies'));

    $response->assertUnauthorized();
});

it('should raise validation errors', function (): void {
    ProvidesTestingData::createRandomUserAndAuthorize();

    $response = $this->jsonWithHeader('POST', $this->url('admin/companies'));

    $response->assertJsonValidationErrors([
        'display_name',
        'country_id',
        'default_currency_id',
        'timezone',
    ]);
});

it('should validate country and currency public ids', function (): void {
    ProvidesTestingData::createRandomUserAndAuthorize([], ['permissions' => $this->getPermissions(['create'])]);

    $response = $this->jsonWithHeader('POST', $this->url('admin/companies'), [
        'display_name'        => 'Invalid References Company',
        'country_id'          => '01J00000000000000000000000',
        'default_currency_id' => '01J00000000000000000000000',
        'timezone'            => 'UTC',
    ]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors([
        'country_id',
        'default_currency_id',
    ]);
});

it('should validate timezone email and website values', function (): void {
    ProvidesTestingData::createRandomUserAndAuthorize([], ['permissions' => $this->getPermissions(['create'])]);
    /** @var Country $country */
    $country = ProvidesTestingData::createCountryRandomItem()->first();
    /** @var Currency $currency */
    $currency = ProvidesTestingData::createCurrencyRandomItem()->first();

    $response = $this->jsonWithHeader('POST', $this->url('admin/companies'), [
        'display_name'        => 'Invalid Company Values',
        'country_id'          => $country->getPublicId(),
        'default_currency_id' => $currency->getPublicId(),
        'timezone'            => 'Invalid/Timezone',
        'support_email'       => 'not-an-email',
        'website_url'         => 'ftp://example.com',
    ]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors([
        'timezone',
        'support_email',
        'website_url',
    ]);
});

it('should raise forbidden', function (): void {
    ProvidesTestingData::createRandomUserAndAuthorize();

    $response = $this->jsonWithHeader('POST', $this->url('admin/companies'), [
        'display_name'        => 'Forbidden Company',
        'country_id'          => ProvidesTestingData::createCountryRandomItem()->first()->getPublicId(),
        'default_currency_id' => ProvidesTestingData::createCurrencyRandomItem()->first()->getPublicId(),
        'timezone'            => 'UTC',
    ]);

    $response->assertForbiddenPermissions($this->getPermissions(['create']));
    $response->assertForbidden();
});

it('should create a company with a public id and generated slug', function (): void {
    ProvidesTestingData::createRandomUserAndAuthorize([], ['permissions' => $this->getPermissions(['create'])]);

    $response = $this->jsonWithHeader('POST', $this->url('admin/companies'), [
        'display_name'        => 'Acme Adventures',
        'country_id'          => ProvidesTestingData::createCountryRandomItem()->first()->getPublicId(),
        'default_currency_id' => ProvidesTestingData::createCurrencyRandomItem()->first()->getPublicId(),
        'timezone'            => 'Europe/Tbilisi',
    ]);

    $response->assertOk();
    $response->assertJsonDataItemStructure($this->getCompanyStructure());

    /** @var Company $company */
    $company = Company::query()->where('public_id', $response->json('data.id'))->firstOrFail();

    expect($company->slug)->toBe('acme-adventures');
    $this->assertDatabaseHas('companies', [
        'id'           => $company->getId(),
        'display_name' => 'Acme Adventures',
        'status'       => 'active',
    ]);
});
