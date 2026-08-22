<?php

declare(strict_types=1);

namespace Database\Factories\Retailer;

use App\Models\General\Country\Country;
use App\Models\General\Currency;
use App\Models\Retailer\Retailer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Retailer>
 */
class RetailerFactory extends Factory
{
    protected $model = Retailer::class;

    public function definition(): array
    {
        $name = fake()->unique()->company();

        return [
            'name'              => $name,
            'slug'              => Str::slug($name),
            'domain'            => fake()->optional()->domainName(),
            'country_id'        => Country::factory(),
            'currency_id'       => Currency::factory(),
            'is_active'         => true,
            'scraping_enabled'  => true,
            'last_scraped_at'   => null,
        ];
    }
}
