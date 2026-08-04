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
        Schema::create('tours', function (Blueprint $table): void {
            /** @var Blueprint&BlueprintMacros $table */
            $table->id();
            $table->publicId();
            $table->string('title', 180);
            $table->text('description');
            $table->string('start_location');
            $table->string('end_location');
            $table->decimal('distance_km', 10, 2);
            $table->unsignedSmallInteger('duration_comfortable_days');
            $table->unsignedSmallInteger('duration_recommended_days');
            $table->decimal('daily_distance_comfortable_km', 8, 2);
            $table->decimal('daily_distance_recommended_km', 8, 2);
            $table->unsignedInteger('elevation_gain_m')->default(0);
            $table->foreignId('difficulty_id')->constrained('difficulties')->restrictOnDelete();
            $table->unsignedBigInteger('price_comfortable');
            $table->unsignedBigInteger('price_recommended');
            $table->unsignedBigInteger('average_daily_spend');
            $table->foreignId('currency_id')->constrained('currencies')->restrictOnDelete();
            $table->foreignId('route_file_id')->constrained('files')->restrictOnDelete();
            $table->foreignId('cover_image_id')->constrained('files')->restrictOnDelete();
            $table->foreignId('publishing_status_id')->constrained('publishing_statuses')->restrictOnDelete();
            $table->timestampsTz();
            $table->softDeletes();

            $table->index(['publishing_status_id', 'difficulty_id']);
            $table->index(['difficulty_id', 'distance_km']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tours');
    }
};
