<?php

declare(strict_types=1);

namespace App\Services\General\Country;

use App\CacheRepositories\General\Country\CountryCacheRepository;
use App\Contracts\Repositories\General\Country\CountryRepositoryContract;
use App\Contracts\Services\General\Country\CountryServiceContract;
use App\Models\General\Country\Country;
use App\Repositories\General\Country\CountryRepository;
use App\Services\Service;
use App\Support\Collection;
use Illuminate\Support\Facades\DB;

class CountryService extends Service implements CountryServiceContract
{
    public function __construct(
        private readonly CountryRepositoryContract $cachedRepository,
        private readonly CountryRepository $repository,
        private readonly CountryCacheRepository $cacheRepository,
    ) {}

    public function getItems(array $filters = [], array $relations = [], bool $checkPermission = true): Collection
    {
        if ($checkPermission) {
            $this->authorize('read', new Country);
        }

        return $this->cachedRepository->getItems($filters, $relations);
    }

    public function getActiveItems(array $relations = [], bool $checkPermission = true): Collection
    {
        if ($checkPermission) {
            $this->authorize('read', new Country);
        }

        return $this->cachedRepository->getActiveItems($relations);
    }

    public function findByPublicId(string $publicId, array $relations = [], bool $checkPermission = true): ?Country
    {
        $country = $this->cachedRepository->findByPublicId($publicId, $relations);

        if ($country === null) {
            return null;
        }

        if ($checkPermission) {
            $this->authorize('read', $country);
        }

        return $country;
    }

    public function create(array $countryData, array $phoneCodes = []): Country
    {
        $this->authorize('create', new Country);

        /** @var Country $country */
        $country = DB::transaction(function () use ($countryData, $phoneCodes): Country {
            $country = $this->repository->create($countryData);

            return $this->repository->syncPhoneCodes($country, $phoneCodes);
        });
        $this->cacheRepository->clearCache();

        return $country;
    }

    public function update(Country $country, array $countryData, ?array $phoneCodes = null): Country
    {
        $this->authorize('update', $country);

        /** @var Country $country */
        $country = DB::transaction(function () use ($country, $countryData, $phoneCodes): Country {
            $country = $this->repository->fillData($country, $countryData);

            if ($phoneCodes !== null) {
                return $this->repository->syncPhoneCodes($country, $phoneCodes);
            }

            return $country->fresh(['phoneCodes']);
        });
        $this->cacheRepository->clearCache();

        return $country;
    }

    public function delete(Country $country): void
    {
        $this->authorize('delete', $country);
        $this->repository->delete($country);
        $this->cacheRepository->clearCache();
    }
}
