<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\UserContactType;
use App\Models\UserContact;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<UserContact> */
class UserContactFactory extends Factory
{
    protected $model = UserContact::class;

    public function definition(): array
    {
        return [
            'type'       => UserContactType::PHONE,
            'label'      => null,
            'value'      => '+'.fake()->numerify('############'),
            'is_primary' => false,
        ];
    }

    public function email(): static
    {
        return $this->state([
            'type'  => UserContactType::EMAIL,
            'value' => fake()->unique()->safeEmail(),
        ]);
    }

    public function address(): static
    {
        return $this->state([
            'type'  => UserContactType::ADDRESS,
            'value' => fake()->address(),
        ]);
    }

    public function primary(): static
    {
        return $this->state(['is_primary' => true]);
    }
}
