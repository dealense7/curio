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
        Schema::create('retailers', function (Blueprint $table): void {
            /** @var Blueprint&BlueprintMacros $table */
            $table->id();
            $table->publicId();
            $table->string('name', 160);
            $table->string('slug', 180)->unique();
            $table->string('domain', 255)->nullable();
            $table->foreignId('country_id')->constrained('countries')->restrictOnDelete();
            $table->foreignId('currency_id')->constrained('currencies')->restrictOnDelete();
            $table->boolean('is_active')->default(true);
            $table->boolean('scraping_enabled')->default(true);
            $table->timestampTz('last_scraped_at')->nullable();
            $table->actorColumns();
            $table->timestampsTz();
            $table->softDeletes();

            $table->index(['is_active', 'scraping_enabled']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('retailers');
    }
};
