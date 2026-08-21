<?php

declare(strict_types=1);

namespace App\Services\General\Currency;

use App\Contracts\Repositories\General\Currency\CurrencyRepositoryContract;
use App\Contracts\Services\General\Currency\CurrencyServiceContract;
use App\Models\General\Currency;
use App\Support\Collection;

class CurrencyService implements CurrencyServiceContract
{
    public function __construct(private readonly CurrencyRepositoryContract $cachedRepository)
    {
        //
    }

    public function getActiveItems(): Collection
    {
        return $this->cachedRepository->getActiveItems();
    }

    public function findByPublicId(string $publicId): ?Currency
    {
        return $this->cachedRepository->findByPublicId($publicId);
    }
}
