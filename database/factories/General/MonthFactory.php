<?php

declare(strict_types=1);

namespace Database\Factories\General;

use App\Enums\General\Month as MonthEnum;
use App\Models\General\Month;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Month> */
class MonthFactory extends Factory
{
    protected $model = Month::class;

    public function definition(): array
    {
        $month = fake()->randomElement(MonthEnum::cases());

        return [
            'key'          => $month,
            'display_name' => $month->getText(),
            'sort_order'   => array_search($month, MonthEnum::cases(), true) + 1,
        ];
    }
}
