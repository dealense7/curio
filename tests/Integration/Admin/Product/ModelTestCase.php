<?php

declare(strict_types=1);

namespace Tests\Integration\Admin\Product;

use App\Models\Product\Product;
use App\Support\Testing\ProvidesTestingData;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Integration\IntegrationTestCase;

class ModelTestCase extends IntegrationTestCase
{
    use DatabaseTransactions;

    protected static function getModel(): Product
    {
        return new Product;
    }

    protected function getPermissions(array $abilities): array
    {
        return array_map(static fn (string $ability): string => Product::getPermission($ability), $abilities);
    }

    protected function getProductData(): array
    {
        $category = ProvidesTestingData::createCategoryRandomItem()->firstOrFail();

        return [
            'category_id' => $category->getPublicId(),
            'name'        => 'Coca-Cola Zero Sugar',
            'brand'       => 'Coca-Cola',
            'gtin'        => '5449000131836',
            'size_value'  => 2,
            'size_unit'   => 'l',
            'pack_count'  => 1,
            'description' => 'Two litre bottle.',
            'is_active'   => true,
        ];
    }
}
