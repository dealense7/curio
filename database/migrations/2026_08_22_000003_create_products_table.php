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
        Schema::create('products', function (Blueprint $table): void {
            /** @var Blueprint&BlueprintMacros $table */
            $table->id();
            $table->publicId();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('name', 160);
            $table->string('brand', 120)->nullable();
            $table->string('gtin', 14)->nullable();
            $table->decimal('size_value', 12, 3)->nullable();
            $table->string('size_unit', 30)->nullable();
            $table->unsignedInteger('pack_count')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestampsTz();
            $table->softDeletes();

            $table->index('gtin');
            $table->index('brand');
            $table->index('name');
            $table->index('category_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
