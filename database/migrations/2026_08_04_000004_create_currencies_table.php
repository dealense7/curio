<?php

declare(strict_types=1);

use App\Support\Database\BlueprintMacros;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('currencies', function (Blueprint $table): void {
            /** @var Blueprint&BlueprintMacros $table */
            $table->id();
            $table->publicId();
            $table->char('code', 3)->unique();
            $table->string('name', 120);
            $table->string('symbol', 12)->nullable();
            $table->unsignedTinyInteger('decimal_places')->default(2);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestampsTz();

            $table->check('code = upper(code)');
            $table->check('decimal_places between 0 and 4');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
