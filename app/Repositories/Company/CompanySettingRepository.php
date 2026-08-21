<?php

declare(strict_types=1);

namespace App\Repositories\Company;

use App\Contracts\Repositories\Company\CompanySettingRepositoryContract;
use App\Models\CompanySetting;
use App\Repositories\Repository;

class CompanySettingRepository extends Repository implements CompanySettingRepositoryContract
{
    public function findByCompanyId(int $companyId): ?CompanySetting
    {
        return $this->getData()
            ->with('company')
            ->where('company_id', $companyId)
            ->first();
    }

    public function findByCompanyPublicId(string $companyPublicId): ?CompanySetting
    {
        return $this->getData()
            ->with('company')
            ->whereHas('company', static function ($query) use ($companyPublicId): void {
                $query->where('public_id', $companyPublicId);
            })
            ->first();
    }

    public function update(CompanySetting $setting, array $data): CompanySetting
    {
        $setting->fill($data);
        $setting->saveOrFail();

        return $setting;
    }

    public function getModel(): CompanySetting
    {
        return new CompanySetting;
    }
}
