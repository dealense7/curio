<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\General\Country\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Country>
 */
class CountryFactory extends Factory
{
    protected $model = Country::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'iso2'          => fake()->unique()->lexify('??'),
            'iso3'          => fake()->unique()->lexify('???'),
            'numeric_code'  => fake()->optional()->unique()->numerify('###'),
            'name'          => fake()->unique()->country(),
            'official_name' => fake()->optional()->sentence(3),
            'is_active'     => true,
            'sort_order'    => 0,
        ];
    }
}
