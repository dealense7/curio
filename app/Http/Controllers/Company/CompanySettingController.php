<?php

declare(strict_types=1);

namespace App\Http\Controllers\Company;

use App\Contracts\Services\Company\CompanySettingServiceContract;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Company\UpdateCompanySettingRequest;
use App\Http\Resources\CompanySettingResource;
use Illuminate\Http\JsonResponse;

class CompanySettingController extends ApiController
{
    public function __construct(private readonly CompanySettingServiceContract $settingService)
    {
        //
    }

    public function show(?string $companyPublicId = null): JsonResponse
    {
        $setting = $this->settingService->getCurrent($companyPublicId);

        if ($setting === null) {
            return $this->resourceNotFound();
        }

        return $this->resource($setting, CompanySettingResource::class);
    }

    public function update(UpdateCompanySettingRequest $request, ?string $companyPublicId = null): JsonResponse
    {
        $setting = $this->settingService->getCurrent($companyPublicId);

        if ($setting === null) {
            return $this->resourceNotFound();
        }

        $data    = $request->validated();
        $setting = $this->settingService->update($setting, $data);

        return $this->resource($setting, CompanySettingResource::class);
    }
}
