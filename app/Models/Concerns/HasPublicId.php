<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use Illuminate\Support\Str;

trait HasPublicId
{
    public static function bootHasPublicId(): void
    {
        static::creating(static function ($model): void {
            if (blank($model->public_id)) {
                $model->public_id = (string) Str::ulid();
            }
        });
    }
}
