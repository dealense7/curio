<?php

declare(strict_types=1);

namespace Database\Factories\General;

use App\Enums\General\File\Disk;
use App\Enums\General\File\Status;
use App\Enums\General\File\Type;
use App\Models\General\File;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<File> */
class FileFactory extends Factory
{
    protected $model = File::class;

    public function definition(): array
    {
        return [
            'uuid'          => (string) Str::uuid7(),
            'extension'     => fake()->fileExtension(),
            'size'          => fake()->numberBetween(1000, 1000000),
            'disk'          => Disk::PUBLIC,
            'type'          => Type::GENERAL,
            'status'        => Status::TEMPORARY,
            'original_name' => fake()->word(),
            'name'          => fake()->word(),
            'folder'        => fake()->word(),
            'mime'          => fake()->mimeType(),
            'fileable_type' => null,
            'fileable_id'   => null,
        ];
    }
}
