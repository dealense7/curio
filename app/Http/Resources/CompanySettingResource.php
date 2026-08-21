<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\CompanySetting;
use App\Support\Resources\JsonResource;

class CompanySettingResource extends JsonResource
{
    protected static array $transformMapping = [
        'distance_unit'                => 'distance_unit',
        'weight_unit'                  => 'weight_unit',
        'dimension_unit'               => 'dimension_unit',
        'date_format'                  => 'date_format',
        'time_format'                  => 'time_format',
        'require_pickup_photo'         => 'require_pickup_photo',
        'require_delivery_photo'       => 'require_delivery_photo',
        'require_recipient_signature'  => 'require_recipient_signature',
        'require_handoff_acceptance'   => 'require_handoff_acceptance',
        'allow_partial_handoff'        => 'allow_partial_handoff',
        'offline_mode_enabled'         => 'offline_mode_enabled',
        'proof_retention_days'         => 'proof_retention_days',
        'created_at'                   => 'created_at',
        'updated_at'                   => 'updated_at',
    ];

    public function __construct(?CompanySetting $resource)
    {
        $this->resource = $resource;
    }
}
