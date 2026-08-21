<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Http\Resources\General\Country\GeneralCountryResource;
use App\Http\Resources\General\CurrencyResource;
use App\Models\Model;
use App\Support\Resources\JsonResource as ApiJsonResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class CompanyConfigResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var array{countries: Collection, currencies: Collection} $config */
        $config = $this->resource;

        return [
            'countries'  => $this->transformLookups($config['countries'], $request, GeneralCountryResource::class),
            'currencies' => $this->transformLookups($config['currencies'], $request, CurrencyResource::class),
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
