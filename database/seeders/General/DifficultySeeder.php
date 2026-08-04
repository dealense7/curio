<?php

declare(strict_types=1);

namespace Database\Seeders\General;

use App\Enums\General\Difficulty;
use App\Models\General\Difficulty as DifficultyModel;
use Illuminate\Database\Seeder;

class DifficultySeeder extends Seeder
{
    public function run(): void
    {
        foreach (Difficulty::cases() as $difficulty) {
            DifficultyModel::query()->updateOrCreate(
                ['key' => $difficulty->value],
                ['display_name' => $difficulty->getText()],
            );
        }
    }
}
