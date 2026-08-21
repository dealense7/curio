<?php

declare(strict_types=1);

use App\Models\Company;
use App\Models\CompanySetting;
use App\Models\User;
use App\Support\Testing\ProvidesTestingData;
use Tests\Feature\Company\ModelTestCase;

uses(ModelTestCase::class);

it('should prevent hard deletion when the company has users', function (): void {
    /** @var Company $company */
    $company = ProvidesTestingData::createCompanyRandomItem()->first();
    User::factory()->for($company)->create();

    expect(fn () => $company->delete())->toThrow(LogicException::class);
});

it('should create exactly one settings record when a company is created', function (): void {
    /** @var Company $company */
    $company = ProvidesTestingData::createCompanyRandomItem()->first();

    $this->assertDatabaseHas('company_settings', [
        'company_id' => $company->getId(),
    ]);
    expect(CompanySetting::query()->where('company_id', $company->getId())->count())->toBe(1);
});
