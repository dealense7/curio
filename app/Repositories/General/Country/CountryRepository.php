<?php

declare(strict_types=1);

namespace App\Repositories\General\Country;

use App\Contracts\Repositories\General\Country\CountryRepositoryContract;
use App\Models\General\Country\Country;
use App\Models\General\Country\CountryPhoneCode;
use App\Repositories\Repository;
use App\Support\Collection;
use Illuminate\Support\Str;

class CountryRepository extends Repository implements CountryRepositoryContract
{
    /**
     * @return list<string>
     */
    private function getCountryColumns(): array
    {
        return [
            'id',
            'public_id',
            'iso2',
            'iso3',
            'numeric_code',
            'name',
            'official_name',
            'is_active',
            'sort_order',
            'created_at',
            'updated_at',
        ];
    }

    /**
     * @return list<string>
     */
    private function getPhoneCodeColumns(): array
    {
        return [
            'id',
            'public_id',
            'country_id',
            'phone_code',
            'is_primary',
            'sort_order',
            'created_at',
            'updated_at',
        ];
    }

    /**
     * @param  array<int, string>  $relations
     * @return array<int|string, mixed>
     */
    private function mapRelations(array $relations): array
    {
        return collect($relations)
            ->mapWithKeys(function (string $relation): array {
                if ($relation !== 'phoneCodes') {
                    return [$relation => $relation];
                }

                return [
                    'phoneCodes' => fn ($query) => $query
                        ->select($this->getPhoneCodeColumns())
                        ->orderBy('sort_order')
                        ->orderBy('phone_code'),
                ];
            })
            ->all();
    }

    public function getItems(array $filters = [], array $relations = []): Collection
    {
        /** @var Collection<int, Country> $items */
        $items = $this->getData()
            ->select($this->getCountryColumns())
            ->with($this->mapRelations($relations))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return $items;
    }

    public function getActiveItems(array $relations = []): Collection
    {
        /** @var Collection<int, Country> $items */
        $items = $this->getData()
            ->select($this->getCountryColumns())
            ->with($this->mapRelations($relations))
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return $items;
    }

    public function findByPublicId(string $publicId, array $relations = []): ?Country
    {
        /** @var ?Country $country */
        $country = $this->getData()
            ->select($this->getCountryColumns())
            ->with($this->mapRelations($relations))
            ->where('public_id', $publicId)
            ->first();

        return $country;
    }

    public function create(array $data): Country
    {
        return $this->fillData($this->getModel(), $data);
    }

    public function fillData(Country $country, array $data): Country
    {
        $country->fill($data);
        $country->saveOrFail();

        return $country;
    }

    public function syncPhoneCodes(Country $country, array $phoneCodes): Country
    {
        $country->loadMissing(['phoneCodes']);

        /** @var array<string, CountryPhoneCode> $existingByPublicId */
        $existingByPublicId = $country->phoneCodes
            ->keyBy(fn (CountryPhoneCode $phoneCode): string => $phoneCode->getPublicId())
            ->all();

        $keptPhoneCodeIds = [];

        foreach ($phoneCodes as $phoneCodeData) {
            $phoneCode = null;
            $publicId = $phoneCodeData['public_id'] ?? null;

            if (is_string($publicId) && isset($existingByPublicId[$publicId])) {
                $phoneCode = $existingByPublicId[$publicId];
            }

            if ($phoneCode === null) {
                $phoneCode = new CountryPhoneCode();
                $phoneCode->forceFill([
                    'public_id' => (string) Str::ulid(),
                ]);
            }

            $phoneCode->fill([
                'country_id' => $country->getId(),
                'phone_code' => $phoneCodeData['phone_code'],
                'is_primary' => $phoneCodeData['is_primary'] ?? false,
                'sort_order' => $phoneCodeData['sort_order'] ?? 0,
            ]);
            $phoneCode->saveOrFail();

            $keptPhoneCodeIds[] = $phoneCode->getId();
        }

        CountryPhoneCode::query()
            ->where('country_id', $country->getId())
            ->when($keptPhoneCodeIds !== [], fn ($query) => $query->whereNotIn('id', $keptPhoneCodeIds))
            ->delete();

        return $country->fresh(['phoneCodes']);
    }

    public function delete(Country $country): void
    {
        $country->delete();
    }

    public function getModel(): Country
    {
        return new Country;
    }
}
