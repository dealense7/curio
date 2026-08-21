<?php

declare(strict_types=1);

namespace App\Contracts\Services\Company;

use App\DataTransferObject\Company\CompanyCreateDto;
use App\Models\Company;
use App\Support\Collection;

interface CompanyServiceContract
{
    /** @return array{countries: Collection, currencies: Collection} */
    public function getConfig(): array;

    public function getItems(): Collection;

    public function findByPublicId(string $publicId): ?Company;

    public function findBySlug(string $slug): ?Company;

    public function create(CompanyCreateDto $data): Company;

    public function suspend(Company $company, string $reason): Company;

    public function reactivate(Company $company): Company;

    public function archive(Company $company): Company;
}
