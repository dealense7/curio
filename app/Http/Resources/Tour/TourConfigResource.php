<?php

declare(strict_types=1);

namespace App\Http\Resources\Tour;

use App\Http\Resources\General\CurrencyResource;
use App\Http\Resources\General\DifficultyResource;
use App\Http\Resources\General\MonthResource;
use App\Http\Resources\General\PublishingStatusResource;
use App\Models\Model;
use App\Support\Resources\JsonResource as ApiJsonResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class TourConfigResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var array<string, Collection> $config */
        $config = $this->resource;

        return [
            'difficulties'            => $this->transformLookups($config['difficulties'], $request, DifficultyResource::class),
            'publishing_statuses'     => $this->transformLookups($config['publishing_statuses'], $request, PublishingStatusResource::class),
            'currencies'              => $this->transformLookups($config['currencies'], $request, CurrencyResource::class),
            'months'                  => $this->transformLookups($config['months'], $request, MonthResource::class),
        ];
    }

    /**
     * @param  Collection<int, Model>  $items
     * @param  class-string<ApiJsonResource>  $resourceClass
     * @return list<array<string, mixed>>
     */
    private function transformLookups(
        Collection $items,
        Request $request,
        string $resourceClass,
    ): array {
        return $items->map(static function (Model $item) use ($request, $resourceClass): array {
            $resource = new $resourceClass($item);
            $resource->setDataWrapper('');

            return $resource->resolve($request);
        })->values()->all();
    }
}
