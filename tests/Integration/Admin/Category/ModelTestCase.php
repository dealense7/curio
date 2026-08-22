<?php

declare(strict_types=1);

namespace Tests\Integration\Admin\Category;

use App\Models\Category\Category;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Integration\IntegrationTestCase;

class ModelTestCase extends IntegrationTestCase
{
    use DatabaseTransactions;

    protected static function getModel(): Category
    {
        return new Category;
    }

    /** @param array<int, string> $abilities */
    protected function getPermissions(array $abilities): array
    {
        return array_map(
            static fn (string $ability): string => Category::getPermission($ability),
            $abilities,
        );
    }

    /** @return array<string, mixed> */
    protected function getCategoryData(): array
    {
        return [
            'name' => 'Food',
            'slug' => 'food',
        ];
    }
}
