<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\CompanyStatus;
use App\Models\Company;
use App\Models\General\Country\Country;
use App\Models\General\Currency;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Company> */
class CompanyFactory extends Factory
{
    protected $model = Company::class;

    public function definition(): array
    {
        return [
            'display_name'        => fake()->company(),
            'legal_name'          => fake()->optional()->company(),
            'slug'                => fake()->unique()->slug(2),
            'status'              => CompanyStatus::ACTIVE,
            'country_id'          => Country::factory(),
            'default_currency_id' => Currency::factory(),
            'timezone'            => 'UTC',
            'default_locale'      => 'en',
            'support_email'       => fake()->optional()->companyEmail(),
            'support_phone'       => '+'.fake()->numerify('############'),
            'website_url'         => fake()->optional()->url(),
            'logo_path'           => null,
        ];
    }

    public function suspended(?string $reason = null): static
    {
        return $this->state([
            'status'            => CompanyStatus::SUSPENDED,
            'suspended_at'      => now('UTC'),
            'suspension_reason' => $reason ?? 'Administrative suspension',
        ]);
    }
}
