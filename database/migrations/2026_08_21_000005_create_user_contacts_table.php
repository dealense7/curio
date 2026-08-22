<?php

declare(strict_types=1);

use App\Enums\User\UserContactType;
use App\Support\Database\BlueprintMacros;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_contacts', function (Blueprint $table): void {
            /** @var Blueprint&BlueprintMacros $table */
            $table->id();
            $table->publicId();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enumString('type', UserContactType::toArray());
            $table->string('label', 80)->nullable();
            $table->text('value');
            $table->boolean('is_primary')->default(false);
            $table->timestampsTz();
            $table->archivable();
            $table->index(['user_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_contacts');
    }
};
