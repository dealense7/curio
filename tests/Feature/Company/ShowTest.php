<?php

declare(strict_types=1);

use App\Models\Company;
use App\Support\Testing\ProvidesTestingData;
use Tests\Feature\Company\ModelTestCase;

uses(ModelTestCase::class);

it('should raise unauthorized', function (): void {
    /** @var Company $company */
    $company = ProvidesTestingData::createCompanyRandomItem()->first();

    $response = $this->jsonWithHeader('GET', $this->url('admin/companies/'.$company->getPublicId()));

    $response->assertUnauthorized();
});

it('should raise forbidden', function (): void {
    /** @var Company $company */
    $company = ProvidesTestingData::createCompanyRandomItem()->first();
    ProvidesTestingData::createRandomUserAndAuthorize(['company_id' => $company->getId()]);

    $response = $this->jsonWithHeader('GET', $this->url('admin/companies/'.$company->getPublicId()));

    $response->assertForbiddenPermissions($this->getPermissions(['read']));
    $response->assertForbidden();
});

it('should raise not found', function (): void {
    ProvidesTestingData::createRandomUserAndAuthorize([], ['permissions' => $this->getPermissions(['read'])]);

    $response = $this->jsonWithHeader('GET', $this->url('admin/companies/01J00000000000000000000000'));

    $response->assertNotFound();
});

it('should show a company', function (): void {
    /** @var Company $company */
    $company = ProvidesTestingData::createCompanyRandomItem()->first();
    ProvidesTestingData::createRandomUserAndAuthorize([], ['permissions' => $this->getPermissions(['read'])]);

    $response = $this->jsonWithHeader('GET', $this->url('admin/companies/'.$company->getPublicId()));

    $response->assertOk();
    $response->assertJsonDataItemStructure($this->getCompanyStructure());
});
