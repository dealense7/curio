<?php

declare(strict_types=1);

namespace App\Http\Controllers\General\Country;

use App\Contracts\Services\General\Country\CountryServiceContract;
use App\Http\Controllers\ApiController;
use App\Http\Resources\General\Country\GeneralCountryResource;
use App\Models\General\Country\Country;
use App\Support\Resources\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class CountryController extends ApiController
{
    public function __construct(private readonly CountryServiceContract $countryService) {}

    public function index(): JsonResponse
    {
        $cacheKey = 'countries:general:index:'.app()->getLocale();

        /** @var array<string, mixed> $payload */
        $payload = Cache::tags([Country::class])->remember($cacheKey, now()->addHour(), function (): array {
            $countries = $this->countryService->getActiveItems(['phoneCodes'], false);

            return ApiResponse::resource(
                __('api.ok'),
                $countries,
                GeneralCountryResource::class,
                ['phoneCodes'],
            )->response()->getData(true);
        });

        return response()->json($payload);
    }
}
