<?php

declare(strict_types=1);

namespace App\Contracts\Repositories\General\Country;

use App\Models\General\Country\Country;
use App\Support\Collection;

interface CountryRepositoryContract
{
    public function getItems(array $filters = [], array $relations = []): Collection;

    public function getActiveItems(array $relations = []): Collection;

    public function findByPublicId(string $publicId, array $relations = []): ?Country;

    public function create(array $data): Country;

    public function fillData(Country $country, array $data): Country;

    /**
     * @param  array<int, array<string, mixed>>  $phoneCodes
     */
    public function syncPhoneCodes(Country $country, array $phoneCodes): Country;

    public function delete(Country $country): void;
}
