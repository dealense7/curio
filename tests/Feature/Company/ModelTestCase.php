<?php

declare(strict_types=1);

namespace Tests\Feature\Company;

use App\Models\Company;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Integration\IntegrationTestCase;

class ModelTestCase extends IntegrationTestCase
{
    use DatabaseTransactions;

    protected static function getModel(): Company
    {
        return new Company;
    }

    /** @param list<string> $abilities */
    public function getPermissions(array $abilities): array
    {
        return array_map(
            static fn (string $ability): string => Company::getPermission($ability),
            $abilities,
        );
    }
}
