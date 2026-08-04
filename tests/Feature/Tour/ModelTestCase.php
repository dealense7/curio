<?php

declare(strict_types=1);

namespace Tests\Feature\Tour;

use App\Models\Tour\Tour;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Integration\IntegrationTestCase;

class ModelTestCase extends IntegrationTestCase
{
    use DatabaseTransactions;

    protected static function getModel(): Tour
    {
        return new Tour;
    }

    /** @param list<string> $abilities */
    public function getPermissions(array $abilities): array
    {
        return array_map(
            static fn (string $ability): string => Tour::getPermission($ability),
            $abilities,
        );
    }
}
