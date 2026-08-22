<?php

declare(strict_types=1);

namespace Database\Seeders\Category;

use App\Models\Category\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $food = Category::query()->updateOrCreate(
            ['slug' => 'food'],
            ['name' => 'Food', 'parent_id' => null],
        );

        $drinks = Category::query()->updateOrCreate(
            ['slug' => 'drinks'],
            ['name' => 'Drinks', 'parent_id' => $food->getId()],
        );

        Category::query()->updateOrCreate(
            ['slug' => 'soft-drinks'],
            ['name' => 'Soft Drinks', 'parent_id' => $drinks->getId()],
        );
    }
}
