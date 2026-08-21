<?php

declare(strict_types=1);

namespace App\Services\Company;

use App\Contracts\Repositories\Company\CompanySettingRepositoryContract;
use App\Contracts\Services\Company\CompanySettingServiceContract;
use App\Models\Company;
use App\Models\CompanySetting;
use App\Models\User;
use App\Services\Service;
use App\Support\Helpers\Helper;
use Illuminate\Support\Facades\DB;

class CompanySettingService extends Service implements CompanySettingServiceContract
{
    public function __construct(private readonly CompanySettingRepositoryContract $repository)
    {
        //
    }

    public function getCurrent(?string $companyPublicId = null): ?CompanySetting
    {
        $user = Helper::getUser();

        if ($user === null) {
            return null;
        }

        if ($companyPublicId !== null) {
            $setting = $this->repository->findByCompanyPublicId($companyPublicId);
        } else {
            $setting = $this->findByUserCompany($user);
        }

        if ($setting !== null) {
            /** @var Company $company */
            $company = $setting->getRelation('company');
            $this->authorize('manageSettings', $company);
        }

        return $setting;
    }

    private function findByUserCompany(User $user): ?CompanySetting
    {
        if ($user->getAttribute('company_id') === null) {
            return null;
        }

        return $this->repository->findByCompanyId((int) $user->getAttribute('company_id'));
    }

    public function update(CompanySetting $setting, array $data): CompanySetting
    {
        /** @var Company $company */
        $company = $setting->getRelation('company');
        $this->authorize('manageSettings', $company);

        return DB::transaction(function () use ($setting, $data): CompanySetting {
            return $this->repository->update($setting, $data);
        });
    }
}
