<?php

declare(strict_types=1);

namespace Database\Factories\General;

use App\Enums\General\Difficulty as DifficultyEnum;
use App\Models\General\Difficulty;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Difficulty> */
class DifficultyFactory extends Factory
{
    protected $model = Difficulty::class;

    public function definition(): array
    {
        $difficulty = fake()->randomElement(DifficultyEnum::cases());

        return ['key' => $difficulty, 'display_name' => $difficulty->getText()];
    }
}
