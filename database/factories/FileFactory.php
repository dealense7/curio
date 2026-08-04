<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\General\FileDisk;
use App\Enums\General\FileStatus;
use App\Enums\General\FileType;
use App\Models\General\File;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<File> */
class FileFactory extends Factory
{
    protected $model = File::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        $extension = fake()->fileExtension();

        return [
            'original_name' => fake()->word().'.'.$extension,
            'name'          => fake()->uuid().'.'.$extension,
            'folder'        => now()->format('Y-m'),
            'extension'     => $extension,
            'mime'          => fake()->mimeType(),
            'size'          => fake()->numberBetween(1_000, 5_000_000),
            'disk'          => FileDisk::PRIVATE,
            'type'          => FileType::GENERAL,
            'status'        => FileStatus::CONFIRMED,
        ];
    }

    public function route(): static
    {
        return $this->state(fn (): array => [
            'extension' => 'gpx',
            'mime'      => 'application/gpx+xml',
            'type'      => FileType::ROUTE,
        ]);
    }

    public function image(): static
    {
        return $this->state(fn (): array => [
            'extension' => 'jpg',
            'mime'      => 'image/jpeg',
            'type'      => FileType::IMAGE,
            'disk'      => FileDisk::PUBLIC,
        ]);
    }
}
