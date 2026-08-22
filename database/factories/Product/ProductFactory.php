<?php

declare(strict_types=1);

namespace Database\Factories\Product;

use App\Models\Product\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Product> */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'category_id' => null,
            'name'        => fake()->unique()->words(3, true),
            'brand'       => fake()->optional()->company(),
            'gtin'        => fake()->optional()->numerify('##############'),
            'size_value'  => fake()->optional()->randomFloat(3, 0.1, 10),
            'size_unit'   => fake()->optional()->randomElement(['g', 'kg', 'ml', 'l']),
            'pack_count'  => fake()->optional()->numberBetween(1, 12),
            'description' => fake()->optional()->sentence(),
            'is_active'   => true,
        ];
    }
}
