<?php

declare(strict_types=1);

namespace App\Contracts\Repositories\Company;

use App\Models\CompanySetting;

interface CompanySettingRepositoryContract
{
    public function findByCompanyId(int $companyId): ?CompanySetting;

    public function findByCompanyPublicId(string $companyPublicId): ?CompanySetting;

    public function update(CompanySetting $setting, array $data): CompanySetting;
}
