<?php

declare(strict_types=1);

use App\Enums\UserStatus;
use App\Support\Database\BlueprintMacros;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            /** @var Blueprint&BlueprintMacros $table */
            $table->id();
            $table->publicId();
            $table->string('first_name', 80);
            $table->string('last_name', 80);
            $table->string('email', 254)->unique();
            $table->timestampTz('email_verified_at')->nullable();
            $table->string('password');
            $table->enumString('status', UserStatus::toArray(), UserStatus::ACTIVE->value);
            $table->string('preferred_locale', 12)->default('en');
            $table->timestampTz('last_login_at')->nullable();
            $table->string('last_login_ip', 45)->nullable();
            $table->rememberToken();
            $table->timestampsTz();
            $table->archivable();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            /** @var Blueprint&BlueprintMacros $table */
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            /** @var Blueprint&BlueprintMacros $table */
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
