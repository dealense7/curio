<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('files', function (Blueprint $table): void {
            $table->uuid('uuid')->primary();
            $table->string('extension');
            $table->unsignedBigInteger('size');
            $table->string('disk');
            $table->string('original_name');
            $table->unsignedInteger('type');
            $table->unsignedInteger('status');
            $table->string('name');
            $table->string('folder');
            $table->string('mime');
            $table->nullableMorphs('fileable');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestampsTz();
            $table->softDeletes();

            $table->index(['uuid', 'type', 'deleted_at']);
            $table->index(['uuid', 'deleted_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
