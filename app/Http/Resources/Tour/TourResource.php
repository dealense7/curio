<?php

declare(strict_types=1);

namespace App\Http\Resources\Tour;

use App\Http\Resources\General\CurrencyResource;
use App\Http\Resources\General\DifficultyResource;
use App\Http\Resources\General\FileResource;
use App\Http\Resources\General\MonthResource;
use App\Http\Resources\General\PublishingStatusResource;
use App\Models\Tour\Tour;
use App\Support\Resources\JsonResource;
use App\Support\Resources\JsonResourceCollection;

class TourResource extends JsonResource
{
    protected static array $transformMapping = [
        'title'                         => 'title',
        'description'                   => 'description',
        'start_location'                => 'start_location',
        'end_location'                  => 'end_location',
        'distance_km'                   => 'distance_km',
        'duration_comfortable_days'     => 'duration_comfortable_days',
        'duration_recommended_days'     => 'duration_recommended_days',
        'daily_distance_comfortable_km' => 'daily_distance_comfortable_km',
        'daily_distance_recommended_km' => 'daily_distance_recommended_km',
        'elevation_gain_m'              => 'elevation_gain_m',
        'price_comfortable'             => 'price_comfortable',
        'price_recommended'             => 'price_recommended',
        'average_daily_spend'           => 'average_daily_spend',
        'created_at'                    => 'created_at',
        'updated_at'                    => 'updated_at',
    ];

    public function __construct(?Tour $resource)
    {
        $this->resource = $resource;
    }

    public function includeDifficulty(): DifficultyResource
    {
        return new DifficultyResource($this->whenLoaded('difficulty'));
    }

    public function includeCurrency(): CurrencyResource
    {
        return new CurrencyResource($this->whenLoaded('currency'));
    }

    public function includePublishingStatus(): PublishingStatusResource
    {
        return new PublishingStatusResource($this->whenLoaded('publishingStatus'));
    }

    public function includeRouteFile(): FileResource
    {
        return new FileResource($this->whenLoaded('routeFile'));
    }

    public function includeCoverImage(): FileResource
    {
        return new FileResource($this->whenLoaded('coverImage'));
    }

    public function includeBestMonths(): JsonResourceCollection
    {
        return new JsonResourceCollection($this->whenLoaded('bestMonths'), MonthResource::class);
    }
}
