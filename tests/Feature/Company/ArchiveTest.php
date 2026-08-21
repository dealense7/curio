<?php

declare(strict_types=1);

use App\Enums\CompanyStatus;
use App\Models\Company;
use App\Support\Testing\ProvidesTestingData;
use Tests\Feature\Company\ModelTestCase;

uses(ModelTestCase::class);

it('should raise unauthorized', function (): void {
    /** @var Company $company */
    $company = ProvidesTestingData::createCompanyRandomItem()->first();

    $response = $this->jsonWithHeader('POST', $this->url('admin/companies/'.$company->getPublicId().'/archive'));

    $response->assertUnauthorized();
});

it('should raise forbidden', function (): void {
    /** @var Company $company */
    $company = ProvidesTestingData::createCompanyRandomItem()->first();
    ProvidesTestingData::createRandomUserAndAuthorize();

    $response = $this->jsonWithHeader('POST', $this->url('admin/companies/'.$company->getPublicId().'/archive'));

    $response->assertForbiddenPermissions($this->getPermissions(['archive']));
    $response->assertForbidden();
});

it('should raise not found', function (): void {
    ProvidesTestingData::createRandomUserAndAuthorize([], ['permissions' => $this->getPermissions(['archive'])]);

    $response = $this->jsonWithHeader('POST', $this->url('admin/companies/01J00000000000000000000000/archive'));

    $response->assertNotFound();
});

it('should archive a company', function (): void {
    /** @var Company $company */
    $company = ProvidesTestingData::createCompanyRandomItem()->first();
    ProvidesTestingData::createRandomUserAndAuthorize([], ['permissions' => $this->getPermissions(['archive'])]);

    $response = $this->jsonWithHeader('POST', $this->url('admin/companies/'.$company->getPublicId().'/archive'));

    $response->assertOk();
    $this->assertDatabaseHas('companies', [
        'id'     => $company->getId(),
        'status' => CompanyStatus::ARCHIVED->value,
    ]);
});
