<?php

declare(strict_types=1);

namespace App\Http\Resources\Category;

use App\Support\Resources\JsonResource;
use App\Support\Resources\JsonResourceCollection;

class CategoryResource extends JsonResource
{
    protected static array $transformMapping = [
        'parent_id'  => 'parent_id',
        'name'       => 'name',
        'slug'       => 'slug',
        'created_at' => 'created_at',
        'updated_at' => 'updated_at',
    ];

    public function __construct(mixed $resource)
    {
        $this->resource = $resource;
    }

    public function includeParent(): self
    {
        return new self($this->whenLoaded('parent'));
    }

    public function includeChildren(): JsonResourceCollection
    {
        return new JsonResourceCollection($this->whenLoaded('children'), self::class);
    }
}
