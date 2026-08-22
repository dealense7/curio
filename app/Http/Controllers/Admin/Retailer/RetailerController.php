<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Retailer;

use App\Contracts\Services\General\Country\CountryServiceContract;
use App\Contracts\Services\General\Currency\CurrencyServiceContract;
use App\Contracts\Services\Retailer\RetailerServiceContract;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Admin\Retailer\IndexRetailerRequest;
use App\Http\Requests\Admin\Retailer\StoreRetailerRequest;
use App\Http\Requests\Admin\Retailer\UpdateRetailerRequest;
use App\Http\Resources\Retailer\RetailerResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class RetailerController extends ApiController
{
    public function index(IndexRetailerRequest $request, RetailerServiceContract $service): JsonResponse
    {
        $filters = $this->getInputFilters($request);
        $page    = $this->getInputPage($request);
        $perPage = $this->getInputPerPage($request);
        $sort    = $this->getInputSort($request);
        $items   = $service->getItemsWithPagination($filters, [], $page, $perPage, $sort);

        return $this->resource($items, RetailerResource::class);
    }

    public function store(
        StoreRetailerRequest $request,
        RetailerServiceContract $service,
        CountryServiceContract $countryService,
        CurrencyServiceContract $currencyService,
    ): JsonResponse {
        $data = $request->validated();

        if (! empty($data['slug']) && $service->slugExists((string) $data['slug'])) {
            $this->throwCustomValidationError('slug', __('retailer.slug_already_exists'));
        }

        $data = $this->resolveReferences($data, $countryService, $currencyService);
        $item = $service->create($data);

        return $this->resource($item, RetailerResource::class)
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(string $retailerPublicId, RetailerServiceContract $service): JsonResponse
    {
        $item = $service->findByPublicId($retailerPublicId);

        if (! $item) {
            return $this->resourceNotFound();
        }

        return $this->resource($item, RetailerResource::class);
    }

    public function update(
        string $retailerPublicId,
        UpdateRetailerRequest $request,
        RetailerServiceContract $service,
        CountryServiceContract $countryService,
        CurrencyServiceContract $currencyService,
    ): JsonResponse {
        $data = $request->validated();
        $item = $service->findByPublicId($retailerPublicId);

        if (! $item) {
            return $this->resourceNotFound();
        }

        if (! empty($data['slug']) && $service->slugExists((string) $data['slug'], $retailerPublicId)) {
            $this->throwCustomValidationError('slug', __('retailer.slug_already_exists'));
        }

        $data = $this->resolveReferences($data, $countryService, $currencyService);
        $item = $service->update($item, $data);

        return $this->resource($item, RetailerResource::class);
    }

    public function destroy(string $retailerPublicId, RetailerServiceContract $service): JsonResponse
    {
        $item = $service->findByPublicId($retailerPublicId);

        if (! $item) {
            return $this->resourceNotFound();
        }

        $service->delete($item);

        return $this->success(__('retailer.deleted_successfully'));
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function resolveReferences(
        array $data,
        CountryServiceContract $countryService,
        CurrencyServiceContract $currencyService,
    ): array {
        if (array_key_exists('country_id', $data)) {
            $country = $countryService->findByPublicId((string) $data['country_id'], checkPermission: false);

            if ($country === null) {
                $this->throwCustomValidationError('country_id', __('retailer.country_not_found'));
            }

            $data['country_id'] = $country->getId();
        }

        if (array_key_exists('currency_id', $data)) {
            $currency = $currencyService->findByPublicId((string) $data['currency_id']);

            if ($currency === null) {
                $this->throwCustomValidationError('currency_id', __('retailer.currency_not_found'));
            }

            $data['currency_id'] = $currency->getId();
        }

        return $data;
    }
}
