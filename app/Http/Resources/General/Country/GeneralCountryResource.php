<?php

declare(strict_types=1);

namespace App\Http\Resources\General\Country;

use App\Models\General\Country\Country;
use App\Support\Resources\JsonResource;
use App\Support\Resources\JsonResourceCollection;

class GeneralCountryResource extends JsonResource
{
    protected static array $transformMapping = [
        'iso2'          => 'iso2',
        'iso3'          => 'iso3',
        'numeric_code'  => 'numeric_code',
        'name'          => 'name',
        'official_name' => 'official_name',
        'sort_order'    => 'sort_order',
    ];

    public function __construct(?Country $resource)
    {
        $this->resource = $resource;
    }

    public function includePhoneCodes(): JsonResourceCollection
    {
        return new JsonResourceCollection($this->whenLoaded('phoneCodes'), CountryPhoneCodeResource::class);
    }
}
