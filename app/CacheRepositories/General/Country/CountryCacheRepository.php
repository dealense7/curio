<?php

declare(strict_types=1);

namespace App\CacheRepositories\General\Country;

use App\CacheRepositories\CacheRepository;
use App\Contracts\Repositories\General\Country\CountryRepositoryContract;
use App\Models\General\Country\Country;
use App\Repositories\General\Country\CountryRepository;
use App\Support\Collection;

class CountryCacheRepository extends CacheRepository implements CountryRepositoryContract
{
    protected string $cacheKey = Country::class;

    public function __construct(private readonly CountryRepository $repository)
    {
    }

    public function getItems(array $filters = [], array $relations = []): Collection
    {
        return $this->remember(
            $this->createKey('items', [$filters, $relations]),
            fn (): Collection => $this->repository->getItems($filters, $relations),
        );
    }

    public function getActiveItems(array $relations = []): Collection
    {
        return $this->remember($this->createKey('active_items', [$relations]),
            fn (): Collection => $this->repository->getActiveItems($relations),
        );
    }

    public function findByPublicId(string $publicId, array $relations = []): ?Country
    {
        return $this->rememberNullable(
            $this->createKey('find_by_public_id', [$publicId, $relations]),
            fn (): ?Country => $this->repository->findByPublicId($publicId, $relations),
        );
    }

    public function clearCache(): void
    {
        $this->clear();
    }

    public function create(array $data): Country
    {
        $country = $this->repository->create($data);
        $this->clear();

        return $country;
    }

    public function fillData(Country $country, array $data): Country
    {
        $country = $this->repository->fillData($country, $data);
        $this->clear();

        return $country;
    }

    public function syncPhoneCodes(Country $country, array $phoneCodes): Country
    {
        $country = $this->repository->syncPhoneCodes($country, $phoneCodes);
        $this->clear();

        return $country;
    }

    public function delete(Country $country): void
    {
        $this->repository->delete($country);
        $this->clear();
    }
}
