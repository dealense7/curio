<?php

declare(strict_types=1);

namespace App\Http\Resources\General;

use App\Models\General\Month;
use App\Support\Resources\JsonResource;

class MonthResource extends JsonResource
{
    protected static array $transformMapping = [
        'key'          => ['key' => 'getKeyValue'],
        'display_name' => 'display_name',
        'sort_order'   => 'sort_order',
    ];

    public function __construct(?Month $resource)
    {
        $this->resource = $resource;
    }
}
