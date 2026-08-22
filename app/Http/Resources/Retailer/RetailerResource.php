<?php

declare(strict_types=1);

namespace App\Http\Resources\Retailer;

use App\Models\Retailer\Retailer;
use App\Support\Resources\JsonResource;

class RetailerResource extends JsonResource
{
    protected static array $transformMapping = [
        'name'              => 'name',
        'slug'              => 'slug',
        'domain'            => 'domain',
        'country_id'        => 'country_id',
        'currency_id'       => 'currency_id',
        'is_active'         => 'is_active',
        'scraping_enabled'  => 'scraping_enabled',
        'last_scraped_at'   => 'last_scraped_at',
        'created_at'        => 'created_at',
        'updated_at'        => 'updated_at',
    ];

    public function __construct(?Retailer $resource)
    {
        $this->resource = $resource;
    }
}
