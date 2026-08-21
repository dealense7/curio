<?php

declare(strict_types=1);

use App\Enums\Company\DimensionUnit;
use App\Enums\Company\DistanceUnit;
use App\Enums\Company\TimeFormat;
use App\Enums\Company\WeightUnit;
use App\Support\Database\BlueprintMacros;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_settings', function (Blueprint $table): void {
            $table->id();
            /** @var Blueprint&BlueprintMacros $table */
            $table->publicId();
            $table->foreignId('company_id')->unique()->constrained('companies')->cascadeOnDelete();
            $table->enumString('distance_unit', DistanceUnit::toArray(), DistanceUnit::KILOMETERS->value);
            $table->enumString('weight_unit', WeightUnit::toArray(), WeightUnit::KILOGRAMS->value);
            $table->enumString('dimension_unit', DimensionUnit::toArray(), DimensionUnit::CENTIMETERS->value);
            $table->string('date_format', 30)->default('Y-m-d');
            $table->enumString('time_format', TimeFormat::toArray(), TimeFormat::TWENTY_FOUR_HOUR->value);
            $table->boolean('require_pickup_photo')->default(false);
            $table->boolean('require_delivery_photo')->default(true);
            $table->boolean('require_recipient_signature')->default(true);
            $table->boolean('require_handoff_acceptance')->default(true);
            $table->boolean('allow_partial_handoff')->default(false);
            $table->boolean('offline_mode_enabled')->default(false);
            $table->unsignedInteger('proof_retention_days')->default(365);
            $table->timestampsTz();

            $table->check('proof_retention_days between 1 and 3650');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_settings');
    }
};
