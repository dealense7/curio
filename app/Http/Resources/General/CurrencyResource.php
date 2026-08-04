<?php

declare(strict_types=1);

namespace App\Http\Resources\General;

use App\Models\General\Currency;
use App\Support\Resources\JsonResource;

class CurrencyResource extends JsonResource
{
    protected static array $transformMapping = [
        'key'          => ['key' => 'getKeyValue'],
        'display_name' => 'display_name',
    ];

    public function __construct(?Currency $resource)
    {
        $this->resource = $resource;
    }
}
