<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\General\Currency as CurrencyEnum;
use App\Enums\General\Difficulty as DifficultyEnum;
use App\Enums\General\PublishingStatus as PublishingStatusEnum;
use App\Models\General\Currency;
use App\Models\General\Difficulty;
use App\Models\General\PublishingStatus;
use App\Models\Tour\Tour;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Tour> */
class TourFactory extends Factory
{
    protected $model = Tour::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'title'                         => fake()->sentence(4),
            'description'                   => fake()->paragraph(),
            'start_location'                => fake()->city(),
            'end_location'                  => fake()->city(),
            'distance_km'                   => fake()->randomFloat(2, 1, 500),
            'duration_comfortable_days'     => fake()->numberBetween(2, 40),
            'duration_recommended_days'     => fake()->numberBetween(1, 30),
            'daily_distance_comfortable_km' => fake()->randomFloat(2, 20, 120),
            'daily_distance_recommended_km' => fake()->randomFloat(2, 40, 180),
            'elevation_gain_m'              => fake()->numberBetween(0, 5_000),
            'difficulty_id'                 => Difficulty::factory()->state([
                'key'          => DifficultyEnum::MODERATE,
                'display_name' => DifficultyEnum::MODERATE->getText(),
            ]),
            'price_comfortable'             => fake()->numberBetween(100, 2_000),
            'price_recommended'             => fake()->numberBetween(200, 3_000),
            'average_daily_spend'           => fake()->numberBetween(30, 200),
            'currency_id'                   => Currency::factory()->state([
                'code'           => CurrencyEnum::USD,
                'name'           => CurrencyEnum::USD->getText(),
                'symbol'         => CurrencyEnum::USD->getSymbol(),
                'decimal_places' => 2,
                'is_active'      => true,
                'sort_order'     => 0,
            ]),
            'route_file_id'                 => FileFactory::new()->route(),
            'cover_image_id'                => FileFactory::new()->image(),
            'publishing_status_id'          => PublishingStatus::factory()->state([
                'key'          => PublishingStatusEnum::PRIVATE,
                'display_name' => PublishingStatusEnum::PRIVATE->getText(),
            ]),
        ];
    }
}
