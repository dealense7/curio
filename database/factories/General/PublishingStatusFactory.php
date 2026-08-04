<?php

declare(strict_types=1);

namespace Database\Factories\General;

use App\Enums\General\PublishingStatus as PublishingStatusEnum;
use App\Models\General\PublishingStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<PublishingStatus> */
class PublishingStatusFactory extends Factory
{
    protected $model = PublishingStatus::class;

    public function definition(): array
    {
        $status = fake()->randomElement(PublishingStatusEnum::cases());

        return ['key' => $status, 'display_name' => $status->getText()];
    }
}
