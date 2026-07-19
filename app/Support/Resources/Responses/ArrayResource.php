<?php

declare(strict_types=1);

namespace App\Support\Resources\Responses;

use App\Support\Resources\JsonResource;

class ArrayResource extends JsonResource
{
    public function __construct(array $resource)
    {
        $this->resource = $resource;
    }

    public function getTransformed(): array
    {
        return $this->resource;
    }
}
