<?php

declare(strict_types=1);

namespace Database\Seeders\General;

use App\Enums\General\Month;
use App\Models\General\Month as MonthModel;
use Illuminate\Database\Seeder;

class MonthSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Month::cases() as $index => $month) {
            MonthModel::query()->updateOrCreate(
                ['key' => $month->value],
                [
                    'display_name' => $month->getText(),
                    'sort_order'   => $index + 1,
                ],
            );
        }
    }
}
