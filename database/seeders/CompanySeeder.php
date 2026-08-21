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

        $company = Company::query()->firstOrCreate(
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

        $company->settings()->updateOrCreate([], [
            'distance_unit'               => 'km',
            'weight_unit'                 => 'kg',
            'dimension_unit'              => 'cm',
            'date_format'                 => 'Y-m-d',
            'time_format'                 => '24h',
            'require_pickup_photo'        => false,
            'require_delivery_photo'      => true,
            'require_recipient_signature' => true,
            'require_handoff_acceptance'  => true,
            'allow_partial_handoff'       => false,
            'offline_mode_enabled'        => false,
            'proof_retention_days'        => 365,
        ]);
    }
}
