<?php

declare(strict_types=1);

use App\Enums\Acl\DefaultRoles;
use App\Models\Company;
use App\Models\CompanySetting;
use App\Support\Testing\ProvidesTestingData;
use Tests\Feature\Company\ModelTestCase;

uses(ModelTestCase::class);

it('should raise unauthorized', function (): void {
    $response = $this->jsonWithHeader('GET', $this->url('company/settings'));

    $response->assertUnauthorized();
});

it('should raise validation errors', function (): void {
    /** @var Company $company */
    $company = ProvidesTestingData::createCompanyRandomItem()->first();
    ProvidesTestingData::createRandomUserAndAuthorize(
        ['company_id' => $company->getId()],
        ['permissions' => [Company::getPermission('read')]],
    );

    $response = $this->jsonWithHeader('PATCH', $this->url('company/settings'), [
        'distance_unit'        => 'yards',
        'time_format'          => '25h',
        'proof_retention_days' => -1,
    ]);

    $response->assertJsonValidationErrors(['distance_unit', 'time_format', 'proof_retention_days']);
});

it('should raise forbidden', function (): void {
    /** @var Company $company */
    $company = ProvidesTestingData::createCompanyRandomItem()->first();
    ProvidesTestingData::createRandomUserAndAuthorize(['company_id' => $company->getId()]);

    $response = $this->jsonWithHeader('PATCH', $this->url('company/settings'), [
        'distance_unit' => 'mi',
    ]);

    $response->assertForbiddenPermissions([Company::getPermission('read')]);
    $response->assertForbidden();
});

it('should raise forbidden without company permission', function (): void {
    /** @var Company $company */
    $company = ProvidesTestingData::createCompanyRandomItem()->first();
    ProvidesTestingData::createRandomUserAndAuthorize(
        ['company_id' => $company->getId()],
        ['permissions' => [Company::getPermission('read')]],
    );

    $response = $this->jsonWithHeader('PATCH', $this->url('company/settings'), [
        'distance_unit' => 'mi',
    ]);

    $response->assertForbiddenPermissions([Company::getPermission('read')]);
    $response->assertForbidden();
});

it('should raise not found when the user has no company', function (): void {
    ProvidesTestingData::createRandomUserAndAuthorize(
        [],
        ['permissions' => [Company::getPermission('read')]],
    );

    $response = $this->jsonWithHeader('GET', $this->url('company/settings'));

    $response->assertNotFound();
});

it('should show the current company settings', function (): void {
    /** @var Company $company */
    $company = ProvidesTestingData::createCompanyRandomItem()->first();
    /** @var CompanySetting $setting */
    $setting = $company->settings;
    ProvidesTestingData::createRandomUserAndAuthorize(
        ['company_id' => $company->getId()],
        ['permissions' => [Company::getPermission('read')]],
    );

    $response = $this->jsonWithHeader('GET', $this->url('company/settings'));

    $response->assertOk();
    $response->assertJsonStructure([
        'data' => [
            'id',
            'type',
            'attributes' => [
                'distance_unit',
                'weight_unit',
                'dimension_unit',
                'date_format',
                'time_format',
                'proof_retention_days',
            ],
        ],
    ]);
    $response->assertJsonPath('data.attributes.distance_unit', 'km');
    $response->assertJsonPath('data.attributes.proof_retention_days', 365);
    $this->assertDatabaseHas('company_settings', [
        'id'         => $setting->getId(),
        'company_id' => $company->getId(),
    ]);
});

it('should not read another company settings', function (): void {
    /** @var Company $companyA */
    $companyA = ProvidesTestingData::createCompanyRandomItem()->first();
    /** @var Company $companyB */
    $companyB = ProvidesTestingData::createCompanyRandomItem()->first();
    $companyB->settings()->update(['distance_unit' => 'mi']);
    ProvidesTestingData::createRandomUserAndAuthorize(
        ['company_id' => $companyA->getId()],
        ['permissions' => [Company::getPermission('read')]],
    );

    $response = $this->jsonWithHeader('GET', $this->url('company/settings'));

    $response->assertOk();
    $response->assertJsonPath('data.attributes.distance_unit', 'km');
    $this->assertDatabaseHas('company_settings', [
        'company_id'    => $companyB->getId(),
        'distance_unit' => 'mi',
    ]);
});

it('should update the current company settings', function (): void {
    /** @var Company $company */
    $company = ProvidesTestingData::createCompanyRandomItem()->first();
    /** @var CompanySetting $setting */
    $setting = $company->settings;
    ProvidesTestingData::createRandomUserAndAuthorize(
        ['company_id' => $company->getId()],
        ['permissions' => [Company::getPermission('read')]],
    );

    $response = $this->jsonWithHeader('PATCH', $this->url('company/settings'), [
        'distance_unit'          => 'mi',
        'weight_unit'            => 'lb',
        'time_format'            => '12h',
        'require_pickup_photo'   => true,
        'proof_retention_days'   => 730,
    ]);

    $response->assertOk();
    $this->assertDatabaseHas('company_settings', [
        'id'                       => $setting->getId(),
        'company_id'               => $company->getId(),
        'distance_unit'            => 'mi',
        'weight_unit'              => 'lb',
        'time_format'              => '12h',
        'require_pickup_photo'     => true,
        'proof_retention_days'     => 730,
    ]);
});

it('should allow a platform administrator to update selected company settings', function (): void {
    /** @var Company $company */
    $company = ProvidesTestingData::createCompanyRandomItem()->first();
    /** @var CompanySetting $setting */
    $setting = $company->settings;
    ProvidesTestingData::createRandomUserAndAuthorize(
        [],
        ['roles' => [DefaultRoles::ADMINISTRATOR->value]],
    );

    $response = $this->jsonWithHeader('PATCH', $this->url('admin/companies/'.$company->getPublicId().'/settings'), [
        'distance_unit' => 'mi',
    ]);

    $response->assertOk();
    $this->assertDatabaseHas('company_settings', [
        'id'            => $setting->getId(),
        'company_id'    => $company->getId(),
        'distance_unit' => 'mi',
    ]);
});
