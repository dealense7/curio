<?php

declare(strict_types=1);

use App\Models\General\Country\Country;
use App\Models\General\Currency;
use App\Support\Testing\ProvidesTestingData;
use Tests\Feature\Company\ModelTestCase;

uses(ModelTestCase::class);

it('should raise unauthorized', function (): void {
    $response = $this->jsonWithHeader('GET', $this->url('admin/companies/config'));

    $response->assertUnauthorized();
});

it('should raise forbidden', function (): void {
    ProvidesTestingData::createRandomUserAndAuthorize();

    $response = $this->jsonWithHeader('GET', $this->url('admin/companies/config'));

    $response->assertForbiddenPermissions($this->getPermissions(['read']));
    $response->assertForbidden();
});

it('should return active countries and currencies', function (): void {
    /** @var Country $country */
    $country = ProvidesTestingData::createCountryRandomItem(['is_active' => true])->first();
    /** @var Currency $currency */
    $currency = ProvidesTestingData::createCurrencyRandomItem(['is_active' => true])->first();
    ProvidesTestingData::createCountryRandomItem(['is_active' => false]);
    ProvidesTestingData::createCurrencyRandomItem(['is_active' => false]);
    ProvidesTestingData::createRandomUserAndAuthorize([], ['permissions' => $this->getPermissions(['read'])]);

    $response = $this->jsonWithHeader('GET', $this->url('admin/companies/config'));

    $response->assertOk();
    $response->assertJsonStructure([
        'data' => [
            'countries',
            'currencies',
        ],
    ]);
    $response->assertJsonPath('data.countries.0.id', $country->getPublicId());
    $response->assertJsonPath('data.currencies.0.id', $currency->getPublicId());
});
