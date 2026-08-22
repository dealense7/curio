<?php

declare(strict_types=1);

namespace App\Http\Resources\Product;

use App\Http\Resources\General\Category\CategoryResource;
use App\Models\Product\Product;
use App\Support\Resources\JsonResource;

class ProductResource extends JsonResource
{
    protected static array $transformMapping = [
        'category_id' => 'category_id',
        'name'        => 'name',
        'brand'       => 'brand',
        'gtin'        => 'gtin',
        'size_value'  => 'size_value',
        'size_unit'   => 'size_unit',
        'pack_count'  => 'pack_count',
        'description' => 'description',
        'is_active'   => 'is_active',
        'created_at'  => 'created_at',
        'updated_at'  => 'updated_at',
    ];

    public function __construct(?Product $resource)
    {
        $this->resource = $resource;
    }

    public function includeCategory(): CategoryResource
    {
        return new CategoryResource($this->whenLoaded('category'));
    }
}
