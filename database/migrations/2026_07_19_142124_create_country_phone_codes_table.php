<?php

declare(strict_types=1);

use App\Support\Database\BlueprintMacros;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('country_phone_codes', function (Blueprint $table) {
            /** @var Blueprint&BlueprintMacros $table */
            $table->id();
            $table->publicId();
            $table->foreignId('country_id')->constrained('countries')->cascadeOnDelete();
            $table->string('phone_code', 8);
            $table->boolean('is_primary')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestampsTz();

            $table->unique(['country_id', 'phone_code']);
            $table->index(['country_id']);
            $table->softDeletes();
        });

        DB::statement(<<<'SQL'
            CREATE UNIQUE INDEX country_phone_codes_one_active_primary
            ON country_phone_codes (country_id)
            WHERE is_primary = true AND deleted_at IS NULL
            SQL);
    }

    public function down(): void
    {
        Schema::dropIfExists('country_phone_codes');
    }
};
