<?php

declare(strict_types=1);

namespace App\Contracts\Services\General\Currency;

use App\Models\General\Currency;
use App\Support\Collection;

interface CurrencyServiceContract
{
    public function getActiveItems(): Collection;

    public function findByPublicId(string $publicId): ?Currency;
}
