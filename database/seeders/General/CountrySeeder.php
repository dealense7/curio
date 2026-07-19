<?php

declare(strict_types=1);

namespace Database\Seeders\General;

use App\Models\General\Country\Country;
use App\Models\General\Country\CountryPhoneCode;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('data/countries.json');

        if (!is_file($path)) {
            throw new \RuntimeException("Country dataset not found at [{$path}].");
        }

        $contents = file_get_contents($path);

        if ($contents === false) {
            throw new \RuntimeException("Unable to read country dataset at [{$path}].");
        }

        /** @var array<int, array<string, mixed>> $countries */
        $countries = json_decode($contents, true, flags: JSON_THROW_ON_ERROR);

        DB::transaction(function () use ($countries): void {
            $now = Carbon::now('UTC');

            $countryRows             = [];
            $countryPhoneCodesByIso2 = [];

            foreach ($countries as $index => $item) {
                $iso2       = strtoupper((string)$item['cca2']);
                $iso3       = strtoupper((string)$item['cca3']);
                $phoneCodes = collect($item['callingCodes'] ?? [])
                    ->filter(fn(mixed $code): bool => is_string($code) && preg_match('/^\+\d{1,11}$/', $code) === 1)
                    ->unique()
                    ->values();

                $countryRows[] = [
                    'public_id'     => (string)Str::ulid(),
                    'iso2'          => $iso2,
                    'iso3'          => $iso3,
                    'numeric_code'  => isset($item['ccn3']) ? (string)$item['ccn3'] : null,
                    'name'          => (string)data_get($item, 'name.common'),
                    'official_name' => data_get($item, 'name.official'),
                    'is_active'     => true,
                    'sort_order'    => $index,
                    'created_at'    => $now,
                    'updated_at'    => $now,
                ];

                $countryPhoneCodesByIso2[$iso2] = $phoneCodes;
            }

            Country::query()->upsert(
                $countryRows,
                ['iso2'],
                ['iso3', 'numeric_code', 'name', 'official_name', 'is_active', 'sort_order', 'updated_at'],
            );

            /** @var Collection<int, Country> $persistedCountries */
            $persistedCountries = Country::query()
                ->whereIn('iso2', array_keys($countryPhoneCodesByIso2))
                ->get()
                ->keyBy(fn(Country $country): string => $country->getIso2());

            $phoneCodeRows = [];
            $countryIds    = [];

            foreach ($countryPhoneCodesByIso2 as $iso2 => $phoneCodes) {
                /** @var ?Country $country */
                $country = $persistedCountries->get($iso2);

                if ($country === null) {
                    continue;
                }

                $countryIds[] = $country->getId();

                foreach ($phoneCodes as $phoneIndex => $phoneCode) {
                    $phoneCodeRows[] = [
                        'public_id'  => (string)Str::ulid(),
                        'country_id' => $country->getId(),
                        'phone_code' => $phoneCode,
                        'is_primary' => $phoneIndex === 0,
                        'sort_order' => $phoneIndex,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }

            CountryPhoneCode::query()->upsert(
                $phoneCodeRows,
                ['country_id', 'phone_code'],
                ['is_primary', 'sort_order', 'updated_at'],
            );

            /** @var array<int, array<int, string>> $allowedPhoneCodesByCountryId */
            $allowedPhoneCodesByCountryId = [];

            foreach ($countryPhoneCodesByIso2 as $iso2 => $phoneCodes) {
                /** @var ?Country $country */
                $country = $persistedCountries->get($iso2);

                if ($country === null) {
                    continue;
                }

                $allowedPhoneCodesByCountryId[$country->getId()] = $phoneCodes->all();
            }

            foreach ($allowedPhoneCodesByCountryId as $countryId => $allowedPhoneCodes) {
                CountryPhoneCode::query()
                    ->where('country_id', $countryId)
                    ->whereNotIn('phone_code', $allowedPhoneCodes)
                    ->delete();
            }
        });
    }
}
