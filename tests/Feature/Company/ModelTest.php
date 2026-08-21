<?php

declare(strict_types=1);

use App\Models\Company;
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
