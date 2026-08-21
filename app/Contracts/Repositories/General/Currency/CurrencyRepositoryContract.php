<?php

declare(strict_types=1);

namespace App\Contracts\Repositories\General\Currency;

use App\Models\General\Currency;
use App\Support\Collection;

interface CurrencyRepositoryContract
{
    public function getActiveItems(): Collection;

    public function findByPublicId(string $publicId): ?Currency;
}
