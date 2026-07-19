<?php

declare(strict_types=1);

use App\Support\Testing\ProvidesTestingData;
use Tests\Integration\Auth\ModelTestCase;

uses(ModelTestCase::class);

it('should raise unauthorized', function (): void {
    $response = $this->jsonWithHeader('GET', $this->url('auth/acl'));

    $response->assertUnauthorized();
});

it('should return list', function (): void {
    $oauthClient = ProvidesTestingData::createRandomOauthClient();
    $user        = ProvidesTestingData::createRandomUsers([
        'email' => ProvidesTestingData::getFaker()->email(),
    ])->first();

    $role = ProvidesTestingData::createRoleRandomItem([
        'name'       => 'test-role',
        'guard_name' => 'web',
    ])->first();

    $permissionForRole = ProvidesTestingData::createPermissionRandomItem([
        'name'       => 'countries.read',
        'guard_name' => 'web',
    ])->first();

    $userPermission = ProvidesTestingData::createPermissionRandomItem([
        'name'       => 'countries.update',
        'guard_name' => 'web',
    ])->first();

    $role->permissions()->attach([$permissionForRole->getKey()]);
    $user->permissions()->attach([$userPermission->getKey()]);
    $user->roles()->attach([$role->getKey()]);

    $response = $this->json('POST', $this->url('auth/token'), [
        'client_id'  => (string) $oauthClient->getKey(),
        'grant_type' => 'internal',
        'login'      => $user->getEmail(),
        'password'   => '12345678',
    ]);

    $response->assertCreated();

    $accessToken = data_get($response->getDecodedContent(), 'data.attributes.access_token');

    $response = $this->jsonWithHeader('GET', $this->url('auth/acl'), [], [
        'Authorization' => 'Bearer '.$accessToken,
    ]);

    $response->assertJsonDataItemStructure($this->getAclStructure());
});
