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

    $response = $this->jsonWithHeader('POST', $this->url('admin/companies/'.$company->getPublicId().'/suspend'));

    $response->assertUnauthorized();
});

it('should raise validation errors', function (): void {
    /** @var Company $company */
    $company = ProvidesTestingData::createCompanyRandomItem()->first();
    ProvidesTestingData::createRandomUserAndAuthorize([], ['permissions' => $this->getPermissions(['suspend'])]);

    $response = $this->jsonWithHeader('POST', $this->url('admin/companies/'.$company->getPublicId().'/suspend'), [
    ]);

    $response->assertJsonValidationErrors(['reason']);
});

it('should raise forbidden', function (): void {
    /** @var Company $company */
    $company = ProvidesTestingData::createCompanyRandomItem()->first();
    ProvidesTestingData::createRandomUserAndAuthorize();

    $response = $this->jsonWithHeader('POST', $this->url('admin/companies/'.$company->getPublicId().'/suspend'), [
        'reason' => 'Compliance review',
    ]);

    $response->assertForbiddenPermissions($this->getPermissions(['suspend']));
    $response->assertForbidden();
});

it('should raise not found', function (): void {
    ProvidesTestingData::createRandomUserAndAuthorize([], ['permissions' => $this->getPermissions(['suspend'])]);

    $response = $this->jsonWithHeader('POST', $this->url('admin/companies/01J00000000000000000000000/suspend'), [
        'reason' => 'Compliance review',
    ]);

    $response->assertNotFound();
});

it('should suspend a company', function (): void {
    /** @var Company $company */
    $company = ProvidesTestingData::createCompanyRandomItem()->first();
    ProvidesTestingData::createRandomUserAndAuthorize([], ['permissions' => $this->getPermissions(['suspend'])]);

    $response = $this->jsonWithHeader('POST', $this->url('admin/companies/'.$company->getPublicId().'/suspend'), [
        'reason' => 'Compliance review',
    ]);

    $response->assertOk();
    $this->assertDatabaseHas('companies', [
        'id'                 => $company->getId(),
        'status'             => CompanyStatus::SUSPENDED->value,
        'suspension_reason'  => 'Compliance review',
    ]);
});
