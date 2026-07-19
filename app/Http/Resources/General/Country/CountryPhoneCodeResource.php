<?php

declare(strict_types=1);

namespace App\Http\Resources\General\Country;

use App\Models\General\Country\CountryPhoneCode;
use App\Support\Resources\JsonResource;

class CountryPhoneCodeResource extends JsonResource
{
    protected static array $transformMapping = [
        'phone_code' => 'phone_code',
        'is_primary' => 'is_primary',
        'sort_order' => 'sort_order',
    ];

    public function __construct(?CountryPhoneCode $resource)
    {
        $this->resource = $resource;
    }
}
