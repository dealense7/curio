<?php

declare(strict_types=1);

namespace App\Http\Resources\General;

use App\Models\General\PublishingStatus;
use App\Support\Resources\JsonResource;

class PublishingStatusResource extends JsonResource
{
    protected static array $transformMapping = [
        'key'          => ['key' => 'getKeyValue'],
        'display_name' => 'display_name',
    ];

    public function __construct(?PublishingStatus $resource)
    {
        $this->resource = $resource;
    }
}
