<?php

declare(strict_types=1);

use App\Models\General\Country\Country;
use App\Models\General\Country\CountryPhoneCode;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('stores countries with normalized ISO codes and default flags', function () {
    $country = Country::query()->create([
        'iso2' => 'de',
        'iso3' => 'deu',
        'numeric_code' => '276',
        'name' => 'Germany',
        'official_name' => 'Federal Republic of Germany',
    ]);

    expect($country->getIso2())->toBe('DE')
        ->and($country->getIso3())->toBe('DEU')
        ->and($country->getIsActive())->toBeTrue()
        ->and($country->getSortOrder())->toBe(0)
        ->and($country->getPublicId())->not->toBe('');
});

it('allows the same calling code to be shared by multiple countries', function () {
    $usa = Country::factory()->create([
        'iso2' => 'US',
        'iso3' => 'USA',
        'name' => 'United States',
    ]);
    $canada = Country::factory()->create([
        'iso2' => 'CA',
        'iso3' => 'CAN',
        'name' => 'Canada',
    ]);

    CountryPhoneCode::factory()->for($usa, 'country')->create([
        'phone_code' => '+1',
    ]);

    CountryPhoneCode::factory()->for($canada, 'country')->create([
        'phone_code' => '+1',
    ]);

    expect($usa->phoneCodes()->count())->toBe(1)
        ->and($canada->phoneCodes()->count())->toBe(1);
});

it('rejects duplicate phone codes within the same country', function () {
    $country = Country::factory()->create();

    CountryPhoneCode::factory()->for($country, 'country')->create([
        'phone_code' => '+49',
    ]);

    expect(fn () => CountryPhoneCode::factory()->for($country, 'country')->create([
        'phone_code' => '+49',
    ]))->toThrow(QueryException::class);
});

it('allows only one primary phone code per country', function () {
    $country = Country::factory()->create();

    CountryPhoneCode::factory()->for($country, 'country')->primary()->create([
        'phone_code' => '+1809',
    ]);

    expect(fn () => CountryPhoneCode::factory()->for($country, 'country')->primary()->create([
        'phone_code' => '+1829',
    ]))->toThrow(QueryException::class);
});

it('rejects invalid phone code formats', function () {
    $country = Country::factory()->create();

    expect(fn () => CountryPhoneCode::factory()->for($country, 'country')->create([
        'phone_code' => '49',
    ]))->toThrow(InvalidArgumentException::class);

    expect(fn () => CountryPhoneCode::factory()->for($country, 'country')->create([
        'phone_code' => '+49A',
    ]))->toThrow(InvalidArgumentException::class);
});

it('deletes phone codes when the parent country is deleted', function () {
    $country = Country::factory()->create();
    $phoneCode = CountryPhoneCode::factory()->for($country, 'country')->primary()->create();

    $country->delete();

    expect(CountryPhoneCode::query()->whereKey($phoneCode->getId())->exists())->toBeFalse();
});

it('resolves the primary phone code relation', function () {
    $country = Country::factory()->create();

    CountryPhoneCode::factory()->for($country, 'country')->create([
        'phone_code' => '+1849',
        'sort_order' => 10,
    ]);

    $primary = CountryPhoneCode::factory()->for($country, 'country')->primary()->create([
        'phone_code' => '+1809',
    ]);

    expect($country->primaryPhoneCode?->getId())->toBe($primary->getId());
});
