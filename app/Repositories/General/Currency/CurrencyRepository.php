<?php

declare(strict_types=1);

namespace App\Repositories\General\Currency;

use App\Contracts\Repositories\General\Currency\CurrencyRepositoryContract;
use App\Models\General\Currency;
use App\Repositories\Repository;
use App\Support\Collection;

class CurrencyRepository extends Repository implements CurrencyRepositoryContract
{
    public function getActiveItems(): Collection
    {
        /** @var Collection<int, Currency> $items */
        $items = $this->getData()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return $items;
    }

    public function findByPublicId(string $publicId): ?Currency
    {
        /** @var ?Currency $currency */
        $currency = $this->getData()
            ->where('public_id', $publicId)
            ->first();

        return $currency;
    }

    public function getModel(): Currency
    {
        return new Currency;
    }
}
