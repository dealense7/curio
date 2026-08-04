<?php

declare(strict_types=1);

namespace Database\Seeders\General;

use App\Enums\General\PublishingStatus;
use App\Models\General\PublishingStatus as PublishingStatusModel;
use Illuminate\Database\Seeder;

class PublishingStatusSeeder extends Seeder
{
    public function run(): void
    {
        foreach (PublishingStatus::cases() as $publishingStatus) {
            PublishingStatusModel::query()->updateOrCreate(
                ['key' => $publishingStatus->value],
                ['display_name' => $publishingStatus->getText()],
            );
        }
    }
}
