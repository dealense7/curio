<?php

declare(strict_types=1);

use App\Support\Database\BlueprintMacros;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('country_phone_codes', function (Blueprint $table) {
            /** @var Blueprint&BlueprintMacros $table */
            $table->id();
            $table->publicId();
            $table->foreignId('country_id')->constrained('countries');
            $table->string('phone_code', 8);
            $table->boolean('is_primary')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestampsTz();

            $table->unique(['country_id', 'phone_code']);
            $table->index(['country_id']);
            $table->softDeletes();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('country_phone_codes');
    }
};
