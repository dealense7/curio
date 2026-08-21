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
        Schema::create('auth_login_attempts', function (Blueprint $table): void {
            /** @var Blueprint&BlueprintMacros $table */
            $table->id();
            $table->publicId();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->companyKey(true);
            $table->string('login', 254);
            $table->boolean('succeeded')->default(true);
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestampTz('attempted_at');
            $table->timestampsTz();
            $table->index(['login', 'attempted_at']);
            $table->index(['user_id', 'attempted_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auth_login_attempts');
    }
};
