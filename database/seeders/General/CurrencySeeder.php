<?php

declare(strict_types=1);

namespace Database\Seeders\General;

use App\Enums\General\Currency;
use App\Models\General\Currency as CurrencyModel;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        foreach (Currency::cases() as $currency) {
            CurrencyModel::query()->updateOrCreate(
                ['key' => $currency->value],
                ['display_name' => $currency->getText()],
            );
        }
    }
}
