<?php

declare(strict_types=1);

use App\Enums\General\Month;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('months', function (Blueprint $table): void {
            $table->id();
            $table->enum('key', Month::toArray())->unique();
            $table->string('display_name');
            $table->unsignedTinyInteger('sort_order')->unique();
            $table->timestampsTz();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('months');
    }
};
