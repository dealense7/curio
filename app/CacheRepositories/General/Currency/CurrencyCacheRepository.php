<?php

declare(strict_types=1);

namespace App\CacheRepositories\General\Currency;

use App\CacheRepositories\CacheRepository;
use App\Contracts\Repositories\General\Currency\CurrencyRepositoryContract;
use App\Models\General\Currency;
use App\Repositories\General\Currency\CurrencyRepository;
use App\Support\Collection;

class CurrencyCacheRepository extends CacheRepository implements CurrencyRepositoryContract
{
    protected string $cacheKey = Currency::class;

    public function __construct(private readonly CurrencyRepository $repository)
    {
        //
    }

    public function getActiveItems(): Collection
    {
        return $this->remember(
            $this->createKey('active_items'),
            fn (): Collection => $this->repository->getActiveItems(),
        );
    }

    public function findByPublicId(string $publicId): ?Currency
    {
        return $this->rememberNullable(
            $this->createKey('find_by_public_id', [$publicId]),
            fn (): ?Currency => $this->repository->findByPublicId($publicId),
        );
    }
}
