<?php

declare(strict_types=1);

namespace App\Support\Traits;

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

    public function getRouteKeyName(): string
    {
        return 'public_id';
    }

    public function getPublicId(): string
    {
        return (string) $this->getAttribute('public_id');
    }

    public function getUuid(): string
    {
        return $this->getPublicId();
    }

    public function getUuidString(): string
    {
        return $this->getPublicId();
    }
}
