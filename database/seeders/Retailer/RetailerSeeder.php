<?php

declare(strict_types=1);

namespace Database\Seeders\Retailer;

use App\Enums\General\Currency as CurrencyEnum;
use App\Models\General\Country\Country;
use App\Models\General\Currency;
use App\Models\Retailer\Retailer;
use Illuminate\Database\Seeder;

class RetailerSeeder extends Seeder
{
    public function run(): void
    {
        $country  = Country::query()->firstWhere('iso2', 'US');
        $currency = Currency::query()->firstWhere('code', CurrencyEnum::USD->value);

        if ($country === null || $currency === null) {
            return;
        }

        $retailers = [
            [
                'name'             => 'Acme Retailer',
                'slug'             => 'acme-retailer',
                'domain'           => 'acme.example.com',
                'is_active'        => true,
                'scraping_enabled' => true,
            ],
            [
                'name'             => 'Northstar Retailer',
                'slug'             => 'northstar-retailer',
                'domain'           => 'northstar.example.com',
                'is_active'        => true,
                'scraping_enabled' => false,
            ],
            [
                'name'             => 'Archived Retailer',
                'slug'             => 'archived-retailer',
                'domain'           => null,
                'is_active'        => false,
                'scraping_enabled' => false,
            ],
        ];

        foreach ($retailers as $retailer) {
            Retailer::query()->updateOrCreate(
                ['slug' => $retailer['slug']],
                [
                    ...$retailer,
                    'country_id'  => $country->getId(),
                    'currency_id' => $currency->getId(),
                ],
            );
        }
    }
}
