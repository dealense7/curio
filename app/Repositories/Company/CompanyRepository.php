<?php

declare(strict_types=1);

namespace App\Repositories\Company;

use App\Contracts\Repositories\Company\CompanyRepositoryContract;
use App\Enums\CompanyStatus;
use App\Models\Company;
use App\Repositories\Repository;
use App\Support\Collection;
use Illuminate\Support\Carbon;

class CompanyRepository extends Repository implements CompanyRepositoryContract
{
    public function getItems(): Collection
    {
        /** @var Collection<int, Company> $items */
        $items = $this->getData()
            ->orderBy('display_name')
            ->get();

        return $items;
    }

    public function findByPublicId(string $publicId): ?Company
    {
        return $this->getData()
            ->where('public_id', $publicId)
            ->first();
    }

    public function findBySlug(string $slug): ?Company
    {
        return $this->getData()->where('slug', $slug)->first();
    }

    public function update(Company $item, array $data, array $relations = []): Company
    {
        $item->fill($data);
        $item->saveOrFail();

        if ($relations !== []) {
            $item->load($relations);
        }

        return $item;
    }

    public function store(array $data, array $relations = [], ?int $createdBy = null): Company
    {
        $item = $this->getModel();
        $item->fill($data);
        $item->created_by = $createdBy;
        $item->saveOrFail();

        if ($relations !== []) {
            $item->load($relations);
        }

        return $item;
    }

    public function getModel(): Company
    {
        return new Company;
    }

    public function suspend(Company $company, string $reason): Company
    {
        return $this->update($company, [
            'status'            => CompanyStatus::SUSPENDED,
            'suspended_at'      => Carbon::now('UTC'),
            'suspension_reason' => trim($reason),
        ]);
    }

    public function reactivate(Company $company): Company
    {
        return $this->update($company, [
            'status'            => CompanyStatus::ACTIVE,
            'suspended_at'      => null,
            'suspension_reason' => null,
        ]);
    }

    public function archive(Company $company): Company
    {
        return $this->update($company, [
            'status'      => CompanyStatus::ARCHIVED,
            'archived_at' => Carbon::now('UTC'),
        ]);
    }
}
