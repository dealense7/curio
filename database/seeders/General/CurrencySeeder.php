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
                ['code' => $currency->value],
                [
                    'name'           => $currency->getText(),
                    'symbol'         => $currency->getSymbol(),
                    'decimal_places' => 2,
                    'is_active'      => true,
                    'sort_order'     => 0,
                ],
            );
        }
    }
}
