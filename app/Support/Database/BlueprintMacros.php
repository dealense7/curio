<?php

declare(strict_types=1);

namespace App\Support\Database;

use Illuminate\Database\Schema\Blueprint;

interface BlueprintMacros
{
    public function publicId(): Blueprint;

    public function companyKey(bool $nullable = false): Blueprint;

    public function archivable(): Blueprint;

    public function optimisticLock(int $default = 1): Blueprint;

    public function actorColumns(): Blueprint;

    /** @param list<string> $values */
    public function enumString(string $column, array $values, ?string $default = null): Blueprint;

    public function money(string $name): Blueprint;

    public function coordinates(string $latitude = 'latitude', string $longitude = 'longitude'): Blueprint;

    public function weight(string $column = 'weight'): Blueprint;

    public function dimensions(string $prefix = ''): Blueprint;
}
