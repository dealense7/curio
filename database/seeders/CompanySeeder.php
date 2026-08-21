<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Company;
use App\Models\General\Country\Country;
use App\Models\General\Currency;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        $country  = Country::query()->firstOrFail();
        $currency = Currency::query()->where('code', 'EUR')->firstOrFail();

        Company::query()->firstOrCreate(
            ['slug' => 'demo-company'],
            [
                'display_name'        => 'Demo Company',
                'legal_name'          => 'Demo Company Ltd',
                'country_id'          => $country->getId(),
                'default_currency_id' => $currency->getId(),
                'timezone'            => 'UTC',
                'default_locale'      => 'en',
                'support_email'       => 'support@example.com',
            ],
        );
    }
}
