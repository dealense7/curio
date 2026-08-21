<?php

declare(strict_types=1);

namespace App\Contracts\Services\Company;

use App\Models\CompanySetting;

interface CompanySettingServiceContract
{
    public function getCurrent(?string $companyPublicId = null): ?CompanySetting;

    public function update(CompanySetting $setting, array $data): CompanySetting;
}
