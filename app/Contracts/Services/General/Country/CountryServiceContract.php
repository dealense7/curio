<?php

declare(strict_types=1);

namespace App\Contracts\Services\General\Country;

use App\Models\General\Country\Country;
use App\Support\Collection;

interface CountryServiceContract
{
    public function getItems(array $filters = [], array $relations = [], bool $checkPermission = true): Collection;

    public function getActiveItems(array $relations = [], bool $checkPermission = true): Collection;

    public function findByPublicId(string $publicId, array $relations = [], bool $checkPermission = true): ?Country;

    /**
     * @param  array<string, mixed>  $countryData
     * @param  array<int, array<string, mixed>>  $phoneCodes
     */
    public function create(array $countryData, array $phoneCodes = []): Country;

    /**
     * @param  array<string, mixed>  $countryData
     * @param  array<int, array<string, mixed>>|null  $phoneCodes
     */
    public function update(Country $country, array $countryData, ?array $phoneCodes = null): Country;

    public function delete(Country $country): void;
}
