<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Country;

use App\Contracts\Services\General\Country\CountryServiceContract;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Admin\Country\StoreCountryRequest;
use App\Http\Requests\Admin\Country\UpdateCountryRequest;
use App\Http\Resources\General\Country\AdminCountryResource;
use Illuminate\Http\JsonResponse;

class CountryController extends ApiController
{
    public function __construct(private readonly CountryServiceContract $countryService)
    {
    }

    public function index(): JsonResponse
    {
        $countries = $this->countryService->getItems(relations: ['phoneCodes']);

        return $this->resource($countries, AdminCountryResource::class, ['phoneCodes']);
    }

    public function store(StoreCountryRequest $request): JsonResponse
    {
        $country = $this->countryService->create(
            $request->validatedCountry(),
            $request->validatedPhoneCodes() ?? [],
        );

        return $this->resource($country->load('phoneCodes'), AdminCountryResource::class, ['phoneCodes']);
    }

    public function show(string $countryPublicId): JsonResponse
    {
        $country = $this->countryService->findByPublicId($countryPublicId, ['phoneCodes']);
        if ($country === null) {
            return $this->resourceNotFound();
        }

        return $this->resource($country, AdminCountryResource::class, ['phoneCodes']);
    }

    public function update(UpdateCountryRequest $request, string $countryPublicId): JsonResponse
    {
        $country = $this->countryService->findByPublicId($countryPublicId, ['phoneCodes']);
        if ($country === null) {
            return $this->resourceNotFound();
        }

        $country = $this->countryService->update(
            $country,
            $request->validatedCountry(),
            $request->validatedPhoneCodes(),
        );

        return $this->resource($country, AdminCountryResource::class, ['phoneCodes']);
    }

    public function destroy(string $countryPublicId): JsonResponse
    {
        $country = $this->countryService->findByPublicId($countryPublicId);
        if ($country === null) {
            return $this->resourceNotFound();
        }

        $this->countryService->delete($country);

        return $this->success(__('country.deleted_successfully'));
    }
}
