<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Company\DimensionUnit;
use App\Enums\Company\DistanceUnit;
use App\Enums\Company\TimeFormat;
use App\Enums\Company\WeightUnit;
use App\Models\Concerns\HasPublicId;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanySetting extends Model
{
    use HasPublicId;

    protected $table = 'company_settings';

    /** @var list<string> */
    protected $fillable = [
        'distance_unit',
        'weight_unit',
        'dimension_unit',
        'date_format',
        'time_format',
        'require_pickup_photo',
        'require_delivery_photo',
        'require_recipient_signature',
        'require_handoff_acceptance',
        'allow_partial_handoff',
        'offline_mode_enabled',
        'proof_retention_days',
    ];

    protected function casts(): array
    {
        return [
            'distance_unit'               => DistanceUnit::class,
            'weight_unit'                 => WeightUnit::class,
            'dimension_unit'              => DimensionUnit::class,
            'time_format'                 => TimeFormat::class,
            'require_pickup_photo'        => 'boolean',
            'require_delivery_photo'      => 'boolean',
            'require_recipient_signature' => 'boolean',
            'require_handoff_acceptance'  => 'boolean',
            'allow_partial_handoff'       => 'boolean',
            'offline_mode_enabled'        => 'boolean',
            'proof_retention_days'        => 'integer',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function getCompanyId(): int
    {
        return (int) $this->getAttribute('company_id');
    }

    public function getDistanceUnit(): DistanceUnit
    {
        return $this->getAttribute('distance_unit');
    }

    public function getWeightUnit(): WeightUnit
    {
        return $this->getAttribute('weight_unit');
    }

    public function getDimensionUnit(): DimensionUnit
    {
        return $this->getAttribute('dimension_unit');
    }

    public function getDateFormat(): string
    {
        return (string) $this->getAttribute('date_format');
    }

    public function getTimeFormat(): TimeFormat
    {
        return $this->getAttribute('time_format');
    }

    public function getRequirePickupPhoto(): bool
    {
        return (bool) $this->getAttribute('require_pickup_photo');
    }

    public function getRequireDeliveryPhoto(): bool
    {
        return (bool) $this->getAttribute('require_delivery_photo');
    }

    public function getRequireRecipientSignature(): bool
    {
        return (bool) $this->getAttribute('require_recipient_signature');
    }

    public function getRequireHandoffAcceptance(): bool
    {
        return (bool) $this->getAttribute('require_handoff_acceptance');
    }

    public function getAllowPartialHandoff(): bool
    {
        return (bool) $this->getAttribute('allow_partial_handoff');
    }

    public function getOfflineModeEnabled(): bool
    {
        return (bool) $this->getAttribute('offline_mode_enabled');
    }

    public function getProofRetentionDays(): int
    {
        return (int) $this->getAttribute('proof_retention_days');
    }
}
