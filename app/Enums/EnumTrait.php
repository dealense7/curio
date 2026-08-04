<?php

declare(strict_types=1);

namespace App\Enums;

use Illuminate\Support\Collection;

/** @mixin \BackedEnum */
trait EnumTrait
{
    /** @return list<int|string> */
    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function toCollection(): Collection
    {
        return collect(self::cases());
    }
}
