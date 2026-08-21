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

    $response = $this->jsonWithHeader('POST', $this->url('admin/companies/'.$company->getPublicId().'/reactivate'));

    $response->assertUnauthorized();
});

it('should raise forbidden', function (): void {
    /** @var Company $company */
    $company = ProvidesTestingData::createCompanyRandomItem()->first();
    ProvidesTestingData::createRandomUserAndAuthorize();

    $response = $this->jsonWithHeader('POST', $this->url('admin/companies/'.$company->getPublicId().'/reactivate'));

    $response->assertForbiddenPermissions($this->getPermissions(['reactivate']));
    $response->assertForbidden();
});

it('should raise not found', function (): void {
    ProvidesTestingData::createRandomUserAndAuthorize([], ['permissions' => $this->getPermissions(['reactivate'])]);

    $response = $this->jsonWithHeader('POST', $this->url('admin/companies/01J00000000000000000000000/reactivate'));

    $response->assertNotFound();
});

it('should reactivate a company', function (): void {
    /** @var Company $company */
    $company = ProvidesTestingData::createCompanyRandomItem()->first();
    $company->update(['status' => CompanyStatus::SUSPENDED, 'suspension_reason' => 'Review']);
    ProvidesTestingData::createRandomUserAndAuthorize([], ['permissions' => $this->getPermissions(['reactivate'])]);

    $response = $this->jsonWithHeader('POST', $this->url('admin/companies/'.$company->getPublicId().'/reactivate'));

    $response->assertOk();
    $this->assertDatabaseHas('companies', [
        'id'                 => $company->getId(),
        'status'             => CompanyStatus::ACTIVE->value,
        'suspended_at'       => null,
        'suspension_reason'  => null,
    ]);
});
