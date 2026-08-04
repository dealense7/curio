<?php

declare(strict_types=1);

namespace App\Http\Resources\General;

use App\Models\General\Difficulty;
use App\Support\Resources\JsonResource;

class DifficultyResource extends JsonResource
{
    protected static array $transformMapping = [
        'key'          => ['key' => 'getKeyValue'],
        'display_name' => 'display_name',
    ];

    public function __construct(?Difficulty $resource)
    {
        $this->resource = $resource;
    }
}
