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
        Schema::create('companies', function (Blueprint $table): void {
            /** @var Blueprint&BlueprintMacros $table */
            $table->id();
            $table->publicId();
            $table->string('display_name', 120);
            $table->string('legal_name', 180)->nullable();
            $table->string('slug', 80)->unique();
            $table->string('status', 30)->default('active');
            $table->foreignId('country_id')->constrained('countries')->restrictOnDelete();
            $table->foreignId('default_currency_id')->constrained('currencies')->restrictOnDelete();
            $table->string('timezone', 64);
            $table->string('default_locale', 12)->default('en');
            $table->string('support_email', 254)->nullable();
            $table->string('support_phone', 32)->nullable();
            $table->string('website_url', 2048)->nullable();
            $table->string('logo_path', 500)->nullable();
            $table->timestampTz('suspended_at')->nullable();
            $table->string('suspension_reason', 500)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestampsTz();
            $table->archivable();

            $table->index(['status', 'country_id']);
            $table->index(['status', 'default_currency_id']);
            $table->check("status in ('active', 'suspended', 'archived')");
            $table->check("status <> 'suspended' or (suspended_at is not null and suspension_reason is not null)");
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
