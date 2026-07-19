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
}
