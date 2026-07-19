<?php

declare(strict_types=1);

namespace Database\Seeders\General;

use App\Models\General\Country\Country;
use App\Models\General\Country\CountryPhoneCode;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('data/countries.json');

        if (! is_file($path)) {
            throw new \RuntimeException("Country dataset not found at [{$path}].");
        }

        $contents = file_get_contents($path);

        if ($contents === false) {
            throw new \RuntimeException("Unable to read country dataset at [{$path}].");
        }

        /** @var array<int, array<string, mixed>> $countries */
        $countries = json_decode($contents, true, flags: JSON_THROW_ON_ERROR);

        DB::transaction(function () use ($countries): void {
            foreach ($countries as $index => $item) {
                $iso2 = strtoupper((string) $item['cca2']);
                $iso3 = strtoupper((string) $item['cca3']);

                $country = Country::query()->firstOrNew(['iso2' => $iso2]);

                if (! $country->exists) {
                    $country->public_id = (string) Str::ulid();
                }

                $country->fill([
                    'iso3'          => $iso3,
                    'numeric_code'  => isset($item['ccn3']) ? (string) $item['ccn3'] : null,
                    'name'          => (string) data_get($item, 'name.common'),
                    'official_name' => data_get($item, 'name.official'),
                    'is_active'     => true,
                    'sort_order'    => $index,
                ]);
                $country->save();

                $phoneCodes = collect($item['callingCodes'] ?? [])
                    ->filter(fn (mixed $code): bool => is_string($code) && preg_match('/^\+\d{1,11}$/', $code) === 1)
                    ->unique()
                    ->values();

                foreach ($phoneCodes as $phoneIndex => $phoneCode) {
                    $countryPhoneCode = CountryPhoneCode::query()->firstOrNew([
                        'country_id' => $country->getId(),
                        'phone_code' => $phoneCode,
                    ]);

                    if (! $countryPhoneCode->exists) {
                        $countryPhoneCode->public_id = (string) Str::ulid();
                    }

                    $countryPhoneCode->fill([
                        'is_primary' => $phoneIndex === 0,
                        'sort_order' => $phoneIndex,
                    ]);
                    $countryPhoneCode->save();
                }

                $country->phoneCodes()->whereNotIn('phone_code', $phoneCodes->all())->delete();
            }
        });
    }
}
