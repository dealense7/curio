<?php

declare(strict_types=1);

namespace App\Services\Company;

use App\Contracts\Repositories\Company\CompanyRepositoryContract;
use App\Contracts\Services\Company\CompanyServiceContract;
use App\Contracts\Services\General\Country\CountryServiceContract;
use App\Contracts\Services\General\Currency\CurrencyServiceContract;
use App\DataTransferObject\Company\CompanyCreateDto;
use App\Models\Company;
use App\Services\Service;
use App\Support\Collection;
use Illuminate\Support\Facades\DB;

class CompanyService extends Service implements CompanyServiceContract
{
    public function __construct(
        private readonly CompanyRepositoryContract $repository,
        private readonly CountryServiceContract $countryService,
        private readonly CurrencyServiceContract $currencyService,
    ) {
        //
    }

    public function getConfig(): array
    {
        $this->authorize('read', new Company);

        return [
            'countries'  => $this->countryService->getActiveItems(checkPermission: false),
            'currencies' => $this->currencyService->getActiveItems(),
        ];
    }

    public function getItems(): Collection
    {
        $this->authorize('read', new Company);

        return $this->repository->getItems();
    }

    public function findByPublicId(string $publicId): ?Company
    {
        $company = $this->repository->findByPublicId($publicId);

        if ($company !== null) {
            $this->authorize('read', $company);
        }

        return $company;
    }

    public function findBySlug(string $slug): ?Company
    {
        return $this->repository->findBySlug($slug);
    }

    public function create(CompanyCreateDto $data): Company
    {
        $this->authorize('create', new Company);

        return DB::transaction(function () use ($data): Company {
            return $this->repository->store($data->toArray(), [], $data->createdBy?->getId());
        });
    }

    public function suspend(Company $company, string $reason): Company
    {
        $this->authorize('suspend', $company);

        return DB::transaction(function () use ($company, $reason): Company {
            return $this->repository->suspend($company, $reason);
        });
    }

    public function reactivate(Company $company): Company
    {
        $this->authorize('reactivate', $company);

        return DB::transaction(function () use ($company): Company {
            return $this->repository->reactivate($company);
        });
    }

    public function archive(Company $company): Company
    {
        $this->authorize('archive', $company);

        return DB::transaction(function () use ($company): Company {
            return $this->repository->archive($company);
        });
    }
}
