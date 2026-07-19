<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\General\Country\Country;
use App\Models\General\Country\CountryPhoneCode;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CountryPhoneCode>
 */
class CountryPhoneCodeFactory extends Factory
{
    protected $model = CountryPhoneCode::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'country_id' => Country::factory(),
            'phone_code' => '+'.fake()->numberBetween(1, 9999),
            'is_primary' => false,
            'sort_order' => 0,
        ];
    }

    public function primary(): static
    {
        return $this->state(fn (): array => [
            'is_primary' => true,
        ]);
    }
}
