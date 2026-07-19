<?php

declare(strict_types=1);

namespace App\Support\Resources\Contracts;

interface UuidAsPrimaryContract
{
    public function getKeyName();

    public function getUuid(): string;

    public function getUuidString(): string;
}
