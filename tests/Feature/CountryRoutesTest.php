<?php

declare(strict_types=1);

use App\Models\Acl\Permission;
use App\Models\General\Country\Country;
use App\Models\General\Country\CountryPhoneCode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('returns only active countries from the general countries endpoint', function () {
    $activeCountry = Country::factory()->create([
        'name' => 'Germany',
        'is_active' => true,
    ]);
    Country::factory()->create([
        'name' => 'Archived Country',
        'is_active' => false,
    ]);

    $response = $this->getJson('/api/general/countries');

    $response->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $activeCountry->getPublicId())
        ->assertJsonPath('data.0.attributes.name', 'Germany')
        ->assertJsonMissingPath('data.0.attributes.is_active')
        ->assertJsonPath('data.0.relationships.phone_codes.data', []);
});

it('creates a country from the admin endpoint when the user has permission', function () {
    $permission = Permission::query()->create([
        'name' => Country::getPermission('create'),
        'display_name' => 'Create countries',
        'guard_name' => 'web',
    ]);
    $user = User::factory()->create();
    $user->givePermissionTo($permission);

    $response = $this
        ->actingAs($user)
        ->postJson('/api/admin/countries', [
            'iso2' => 'ge',
            'iso3' => 'geo',
            'numeric_code' => '268',
            'name' => 'Georgia',
            'official_name' => 'Georgia',
            'is_active' => true,
            'sort_order' => 10,
            'phone_codes' => [
                [
                    'phone_code' => '+995',
                    'is_primary' => true,
                    'sort_order' => 0,
                ],
            ],
        ]);

    $response->assertOk()
        ->assertJsonPath('data.attributes.iso2', 'GE')
        ->assertJsonPath('data.attributes.is_active', true)
        ->assertJsonPath('data.relationships.phone_codes.data.0.attributes.phone_code', '+995');

    expect(Country::query()->where('iso2', 'GE')->exists())->toBeTrue();
});

it('forbids admin country creation without permission', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->postJson('/api/admin/countries', [
            'iso2' => 'ge',
            'iso3' => 'geo',
            'name' => 'Georgia',
        ])
        ->assertForbidden();
});

it('keeps existing phone codes when an admin updates a country without sending phone codes', function () {
    $readPermission = Permission::query()->create([
        'name' => Country::getPermission('read'),
        'display_name' => 'View countries',
        'guard_name' => 'web',
    ]);
    $updatePermission = Permission::query()->create([
        'name' => Country::getPermission('update'),
        'display_name' => 'Update countries',
        'guard_name' => 'web',
    ]);
    $user = User::factory()->create();
    $user->givePermissionTo($readPermission, $updatePermission);

    $country = Country::factory()->create([
        'iso2' => 'GE',
        'iso3' => 'GEO',
        'name' => 'Georgia',
    ]);
    $phoneCode = CountryPhoneCode::factory()->for($country, 'country')->primary()->create([
        'phone_code' => '+995',
    ]);

    $response = $this
        ->actingAs($user)
        ->putJson('/api/admin/countries/'.$country->getPublicId(), [
            'iso2' => 'GE',
            'iso3' => 'GEO',
            'name' => 'Georgia Updated',
            'official_name' => 'Georgia Updated',
            'is_active' => true,
            'sort_order' => 5,
        ]);

    $response->assertOk()
        ->assertJsonPath('data.attributes.name', 'Georgia Updated')
        ->assertJsonPath('data.relationships.phone_codes.data.0.id', $phoneCode->getPublicId())
        ->assertJsonPath('data.relationships.phone_codes.data.0.attributes.phone_code', '+995');

    expect(
        CountryPhoneCode::query()
            ->where('country_id', $country->getId())
            ->where('phone_code', '+995')
            ->exists()
    )->toBeTrue();
});
