<?php

declare(strict_types=1);

namespace Database\Seeders\Product;

use App\Models\General\Category\Category;
use App\Models\Product\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $food = Category::query()->where('slug', 'food')->first();
        Product::query()->updateOrCreate(
            ['gtin' => '5449000131836'],
            [
                'category_id' => $food?->getId(),
                'name'        => 'Coca-Cola Zero Sugar',
                'brand'       => 'Coca-Cola',
                'size_value'  => 2,
                'size_unit'   => 'l',
                'pack_count'  => 1,
                'is_active'   => true,
            ],
        );
    }
}
