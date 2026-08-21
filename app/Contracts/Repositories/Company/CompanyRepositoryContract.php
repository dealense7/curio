<?php

declare(strict_types=1);

namespace App\Contracts\Repositories\Company;

use App\Models\Company;
use App\Support\Collection;

interface CompanyRepositoryContract
{
    public function getItems(): Collection;

    public function findByPublicId(string $publicId): ?Company;

    public function findBySlug(string $slug): ?Company;

    /** @param list<string> $relations */
    public function update(Company $item, array $data, array $relations = []): Company;

    /** @param list<string> $relations */
    public function store(array $data, array $relations = [], ?int $createdBy = null): Company;

    public function suspend(Company $company, string $reason): Company;

    public function reactivate(Company $company): Company;

    public function archive(Company $company): Company;
}
