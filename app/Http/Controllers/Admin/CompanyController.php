<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Contracts\Services\Company\CompanyServiceContract;
use App\Contracts\Services\General\Country\CountryServiceContract;
use App\Contracts\Services\General\Currency\CurrencyServiceContract;
use App\DataTransferObject\Company\CompanyCreateDto;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Admin\Company\StoreCompanyRequest;
use App\Http\Requests\Admin\Company\SuspendCompanyRequest;
use App\Http\Resources\CompanyConfigResource;
use App\Http\Resources\CompanyResource;
use App\Support\Helpers\Helper;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class CompanyController extends ApiController
{
    public function __construct(private readonly CompanyServiceContract $companyService)
    {
        //
    }

    public function index(): JsonResponse
    {
        $items = $this->companyService->getItems();

        return $this->resource($items, CompanyResource::class);
    }

    public function config(): JsonResponse
    {
        $config = $this->companyService->getConfig();

        return (new CompanyConfigResource($config))
            ->additional(['message' => __('api.ok')])
            ->response();
    }

    public function store(
        StoreCompanyRequest $request,
        CountryServiceContract $countryService,
        CurrencyServiceContract $currencyService,
    ): JsonResponse {
        $data = $request->validated();

        if (isset($data['slug']) && $this->companyService->findBySlug($data['slug']) !== null) {
            throw ValidationException::withMessages(['slug' => 'The slug has already been taken.']);
        }

        $country = $countryService->findByPublicId($data['country_id'], checkPermission: false);
        if ($country === null) {
            throw ValidationException::withMessages(['country_id' => 'The selected country is invalid.']);
        }

        $currency = $currencyService->findByPublicId($data['default_currency_id']);
        if ($currency === null) {
            throw ValidationException::withMessages(['default_currency_id' => 'The selected currency is invalid.']);
        }

        $companyDto = CompanyCreateDto::fromValidatedData(
            data: $data,
            countryId: $country->getId(),
            defaultCurrencyId: $currency->getId(),
            createdBy: Helper::getUser(),
        );

        $item = $this->companyService->create($companyDto);

        return $this->resource($item, CompanyResource::class);
    }

    public function show(string $companyPublicId): JsonResponse
    {
        $company = $this->companyService->findByPublicId($companyPublicId);

        if ($company === null) {
            return $this->resourceNotFound();
        }

        return $this->resource($company, CompanyResource::class);
    }

    public function suspend(SuspendCompanyRequest $request, string $companyPublicId): JsonResponse
    {
        $data    = $request->validated();
        $company = $this->companyService->findByPublicId($companyPublicId);

        if ($company === null) {
            return $this->resourceNotFound();
        }

        $company = $this->companyService->suspend($company, $data['reason']);

        return $this->resource($company, CompanyResource::class);
    }

    public function reactivate(string $companyPublicId): JsonResponse
    {
        $company = $this->companyService->findByPublicId($companyPublicId);

        if ($company === null) {
            return $this->resourceNotFound();
        }

        $company = $this->companyService->reactivate($company);

        return $this->resource($company, CompanyResource::class);
    }

    public function archive(string $companyPublicId): JsonResponse
    {
        $company = $this->companyService->findByPublicId($companyPublicId);

        if ($company === null) {
            return $this->resourceNotFound();
        }

        $company = $this->companyService->archive($company);

        return $this->resource($company, CompanyResource::class);
    }
}
