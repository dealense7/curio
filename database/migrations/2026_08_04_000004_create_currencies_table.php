<?php

declare(strict_types=1);

use App\Enums\General\Currency;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('currencies', function (Blueprint $table): void {
            $table->id();
            $table->enum('key', Currency::toArray())->unique();
            $table->string('display_name');
            $table->timestampsTz();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
