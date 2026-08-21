<?php

declare(strict_types=1);

namespace App\Http\Resources\General;

use App\Models\General\Currency;
use App\Support\Resources\JsonResource;

class CurrencyResource extends JsonResource
{
    protected static array $transformMapping = [
        'code'           => ['code' => 'getCodeValue'],
        'name'           => 'name',
        'symbol'         => 'symbol',
        'decimal_places' => 'decimal_places',
        'is_active'      => 'is_active',
        'sort_order'     => 'sort_order',
    ];

    public function __construct(?Currency $resource)
    {
        $this->resource = $resource;
    }
}
