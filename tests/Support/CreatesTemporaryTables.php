<?php

declare(strict_types=1);

namespace Tests\Support;

use Closure;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

trait CreatesTemporaryTables
{
    protected function recreateTable(string $table, Closure $definition): void
    {
        Schema::dropIfExists($table);

        Schema::create($table, function (Blueprint $blueprint) use ($definition): void {
            $definition($blueprint);
        });
    }
}
