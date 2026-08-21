<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Company;

class CompanyResource extends JsonResource
{
    protected static array $transformMapping = [
        'display_name'        => 'display_name',
        'legal_name'          => 'legal_name',
        'slug'                => 'slug',
        'status'              => 'status',
        'country_id'          => 'country_id',
        'default_currency_id' => 'default_currency_id',
        'timezone'            => 'timezone',
        'default_locale'      => 'default_locale',
        'support_email'       => 'support_email',
        'support_phone'       => 'support_phone',
        'website_url'         => 'website_url',
        'logo_path'           => 'logo_path',
        'suspended_at'        => 'suspended_at',
        'suspension_reason'   => 'suspension_reason',
        'created_at'          => 'created_at',
        'updated_at'          => 'updated_at',
        'archived_at'         => 'archived_at',
    ];

    public function __construct(?Company $resource)
    {
        $this->resource = $resource;
    }
}
