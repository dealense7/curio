<?php

declare(strict_types=1);

use App\Enums\General\FileDisk;
use App\Enums\General\FileStatus;
use App\Enums\General\FileType;
use App\Support\Database\BlueprintMacros;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('files', function (Blueprint $table): void {
            /** @var Blueprint&BlueprintMacros $table */
            $table->id();
            $table->publicId();
            $table->string('original_name');
            $table->string('name');
            $table->string('folder');
            $table->string('extension', 20);
            $table->string('mime', 120);
            $table->unsignedBigInteger('size');
            $table->enum('disk', FileDisk::toArray())->default(FileDisk::PRIVATE->value);
            $table->enum('type', FileType::toArray())->default(FileType::GENERAL->value);
            $table->enum('status', FileStatus::toArray())->default(FileStatus::TEMPORARY->value);
            $table->timestampsTz();
            $table->softDeletes();

            $table->index(['type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
