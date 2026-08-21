<?php

declare(strict_types=1);

namespace App\Http\Requests\Company;

use App\Enums\Company\DimensionUnit;
use App\Enums\Company\DistanceUnit;
use App\Enums\Company\TimeFormat;
use App\Enums\Company\WeightUnit;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCompanySettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'distance_unit'               => ['sometimes', 'string', Rule::enum(DistanceUnit::class)],
            'weight_unit'                 => ['sometimes', 'string', Rule::enum(WeightUnit::class)],
            'dimension_unit'              => ['sometimes', 'string', Rule::enum(DimensionUnit::class)],
            'date_format'                 => ['sometimes', 'string', 'max:30'],
            'time_format'                 => ['sometimes', 'string', Rule::enum(TimeFormat::class)],
            'require_pickup_photo'        => ['sometimes', 'boolean'],
            'require_delivery_photo'      => ['sometimes', 'boolean'],
            'require_recipient_signature' => ['sometimes', 'boolean'],
            'require_handoff_acceptance'  => ['sometimes', 'boolean'],
            'allow_partial_handoff'       => ['sometimes', 'boolean'],
            'offline_mode_enabled'        => ['sometimes', 'boolean'],
            'proof_retention_days'        => ['sometimes', 'integer', 'between:1,3650'],
        ];
    }
}
