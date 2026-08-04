<?php

declare(strict_types=1);

namespace Database\Factories\General;

use App\Enums\General\Currency as CurrencyEnum;
use App\Models\General\Currency;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Currency> */
class CurrencyFactory extends Factory
{
    protected $model = Currency::class;

    public function definition(): array
    {
        $currency = fake()->randomElement(CurrencyEnum::cases());

        return ['key' => $currency, 'display_name' => $currency->getText()];
    }
}
