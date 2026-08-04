<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tour_best_months', function (Blueprint $table): void {
            $table->foreignId('tour_id')->constrained('tours')->cascadeOnDelete();
            $table->foreignId('month_id')->constrained('months')->restrictOnDelete();

            $table->unique(['tour_id', 'month_id']);
            $table->index(['month_id', 'tour_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tour_best_months');
    }
};
