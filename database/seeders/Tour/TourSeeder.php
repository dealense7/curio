<?php

declare(strict_types=1);

namespace Database\Seeders\Tour;

use App\Enums\General\Currency as CurrencyEnum;
use App\Enums\General\Difficulty as DifficultyEnum;
use App\Enums\General\FileDisk;
use App\Enums\General\FileStatus;
use App\Enums\General\FileType;
use App\Enums\General\Month as MonthEnum;
use App\Enums\General\PublishingStatus as PublishingStatusEnum;
use App\Models\General\Currency;
use App\Models\General\Difficulty;
use App\Models\General\File;
use App\Models\General\Month;
use App\Models\General\PublishingStatus;
use App\Models\Tour\Tour;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TourSeeder extends Seeder
{
    public function run(): void
    {
        $currencyId         = Currency::query()->where('key', CurrencyEnum::EUR->value)->valueOrFail('id');
        $publishingStatusId = PublishingStatus::query()
            ->where('key', PublishingStatusEnum::PUBLISHED->value)
            ->valueOrFail('id');

        foreach ($this->tours() as $data) {
            $difficultyId = Difficulty::query()->where('key', $data['difficulty']->value)->valueOrFail('id');
            $routeFileId  = $this->file($data['route_file'], FileType::ROUTE, FileDisk::PRIVATE, 'application/gpx+xml');
            $coverImageId = $this->file($data['cover_image'], FileType::IMAGE, FileDisk::PUBLIC, 'image/jpeg');

            $tour = Tour::query()->firstOrNew(['title' => $data['title']]);
            if (! $tour->exists) {
                $tour->setAttribute('public_id', (string) Str::ulid());
            }

            $tour->fill([
                'description'                    => $data['description'],
                'start_location'                 => $data['start_location'],
                'end_location'                   => $data['end_location'],
                'distance_km'                    => $data['distance_km'],
                'elevation_gain_m'               => $data['elevation_gain_m'],
                'difficulty_id'                  => $difficultyId,
                'duration_comfortable_days'      => $data['duration_comfortable_days'],
                'duration_recommended_days'      => $data['duration_recommended_days'],
                'daily_distance_comfortable_km'  => $data['daily_distance_comfortable_km'],
                'daily_distance_recommended_km'  => $data['daily_distance_recommended_km'],
                'price_comfortable'              => $data['price_comfortable'],
                'price_recommended'              => $data['price_recommended'],
                'average_daily_spend'            => $data['average_daily_spend'],
                'currency_id'                    => $currencyId,
                'route_file_id'                  => $routeFileId,
                'cover_image_id'                 => $coverImageId,
                'publishing_status_id'           => $publishingStatusId,
            ])->saveOrFail();

            $monthIds = Month::query()->whereIn('key', array_map(
                static fn (MonthEnum $month): string => $month->value,
                $data['best_months'],
            ))->pluck('id')->all();

            $tour->bestMonths()->sync($monthIds);
        }
    }

    private function file(string $path, FileType $type, FileDisk $disk, string $mime): int
    {
        $name      = basename($path);
        $folder    = trim(dirname($path), '/');
        $extension = pathinfo($name, PATHINFO_EXTENSION);

        $file = File::query()->firstOrNew(['folder' => $folder, 'name' => $name]);
        if (! $file->exists) {
            $file->setAttribute('public_id', (string) Str::ulid());
        }

        $file->fill([
            'original_name' => $name,
            'extension'     => $extension,
            'mime'          => $mime,
            'size'          => 0,
            'disk'          => $disk,
            'type'          => $type,
            'status'        => FileStatus::CONFIRMED,
        ])->saveOrFail();

        return $file->getId();
    }

    /** @return list<array<string, mixed>> */
    private function tours(): array
    {
        return [
            [
                'title'                         => 'Scandinavia to Greece',
                'description'                   => 'A long-distance cycling journey from Copenhagen to Athens.',
                'start_location'                => 'Copenhagen, Denmark',
                'end_location'                  => 'Athens, Greece',
                'distance_km'                   => 3850,
                'elevation_gain_m'              => 42800,
                'difficulty'                    => DifficultyEnum::MODERATE,
                'duration_comfortable_days'     => 36,
                'duration_recommended_days'     => 28,
                'daily_distance_comfortable_km' => 107,
                'daily_distance_recommended_km' => 138,
                'price_comfortable'             => 1450,
                'price_recommended'             => 2200,
                'average_daily_spend'           => 65,
                'best_months'                   => [MonthEnum::MAY, MonthEnum::JUNE, MonthEnum::JULY, MonthEnum::AUGUST, MonthEnum::SEPTEMBER],
                'route_file'                    => '/routes/scandinavia-to-greece.gpx',
                'cover_image'                   => '/images/tours/scandinavia-greece-cover.jpg',
            ],
            [
                'title'                         => 'Danube Cycle Adventure',
                'description'                   => 'Follow the Danube River from Vienna to Budapest.',
                'start_location'                => 'Vienna, Austria',
                'end_location'                  => 'Budapest, Hungary',
                'distance_km'                   => 340,
                'elevation_gain_m'              => 1850,
                'difficulty'                    => DifficultyEnum::EASY,
                'duration_comfortable_days'     => 8,
                'duration_recommended_days'     => 6,
                'daily_distance_comfortable_km' => 43,
                'daily_distance_recommended_km' => 57,
                'price_comfortable'             => 520,
                'price_recommended'             => 750,
                'average_daily_spend'           => 90,
                'best_months'                   => [MonthEnum::APRIL, MonthEnum::MAY, MonthEnum::JUNE, MonthEnum::JULY, MonthEnum::AUGUST, MonthEnum::SEPTEMBER, MonthEnum::OCTOBER],
                'route_file'                    => '/routes/danube-cycle-adventure.gpx',
                'cover_image'                   => '/images/tours/danube-cover.jpg',
            ],
            [
                'title'                         => 'Tuscany Hills Tour',
                'description'                   => 'Cycle through the vineyards and medieval towns of Tuscany.',
                'start_location'                => 'Florence, Italy',
                'end_location'                  => 'Siena, Italy',
                'distance_km'                   => 410,
                'elevation_gain_m'              => 6200,
                'difficulty'                    => DifficultyEnum::CHALLENGING,
                'duration_comfortable_days'     => 10,
                'duration_recommended_days'     => 8,
                'daily_distance_comfortable_km' => 41,
                'daily_distance_recommended_km' => 51,
                'price_comfortable'             => 760,
                'price_recommended'             => 1100,
                'average_daily_spend'           => 120,
                'best_months'                   => [MonthEnum::APRIL, MonthEnum::MAY, MonthEnum::JUNE, MonthEnum::SEPTEMBER, MonthEnum::OCTOBER],
                'route_file'                    => '/routes/tuscany-hills.gpx',
                'cover_image'                   => '/images/tours/tuscany-cover.jpg',
            ],
        ];
    }
}
