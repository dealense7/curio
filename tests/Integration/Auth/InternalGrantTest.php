<?php

declare(strict_types=1);

use App\Support\Testing\ProvidesTestingData;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Laravel\Passport\Client;
use Tests\Integration\Auth\ModelTestCase;

uses(ModelTestCase::class);

it('should return error on invalid client', function (): void {
    $user = ProvidesTestingData::createRandomUsers([
        'email' => ProvidesTestingData::getFaker()->email(),
    ])->first();

    $response = $this->json('POST', $this->url('auth/token'), [
        'client_id'  => Str::uuid()->toString(),
        'grant_type' => 'internal',
        'login'      => $user->getEmail(),
        'password'   => '12345678',
    ]);

    $response->assertAuthenticationFailed();
});

it('should return error on invalid grant', function (): void {
    $oauthClient = ProvidesTestingData::createRandomOauthClient();
    $user        = ProvidesTestingData::createRandomUsers([
        'email' => ProvidesTestingData::getFaker()->email(),
    ])->first();

    $response = $this->json('POST', $this->url('auth/token'), [
        'client_id'  => (string) $oauthClient->getKey(),
        'grant_type' => 'wrong_grant',
        'login'      => $user->getEmail(),
        'password'   => '12345678',
    ]);

    $response->assertGrantTypeFailed();
});

it('should reject a client that is not authorized for the internal grant', function (): void {
    $oauthClient = Client::factory()->create([
        'secret'      => null,
        'grant_types' => ['authorization_code', 'refresh_token'],
    ]);
    $user = ProvidesTestingData::createRandomUsers()->first();

    $response = $this->json('POST', $this->url('auth/token'), [
        'client_id'  => (string) $oauthClient->getKey(),
        'grant_type' => 'internal',
        'login'      => $user->getEmail(),
        'password'   => '12345678',
    ]);

    $response->assertStatus(400);
    $response->assertErrorMessage(__('oauth.unauthorized_client'));
});

it('should require and validate the secret for a confidential client', function (): void {
    $oauthClient = ProvidesTestingData::createRandomOauthClient([
        'secret' => 'correct-client-secret',
    ]);
    $user    = ProvidesTestingData::createRandomUsers()->first();
    $payload = [
        'client_id'  => (string) $oauthClient->getKey(),
        'grant_type' => 'internal',
        'login'      => $user->getEmail(),
        'password'   => '12345678',
    ];

    $this->json('POST', $this->url('auth/token'), $payload)
        ->assertStatus(400);

    $this->json('POST', $this->url('auth/token'), [
        ...$payload,
        'client_secret' => 'wrong-client-secret',
    ])->assertAuthenticationFailed();

    $this->json('POST', $this->url('auth/token'), [
        ...$payload,
        'client_secret' => 'correct-client-secret',
    ])->assertCreated();
});

it('should return user by internal grant', function (): void {
    $oauthClient = ProvidesTestingData::createRandomOauthClient();
    $user        = ProvidesTestingData::createRandomUsers([
        'email' => ProvidesTestingData::getFaker()->email(),
    ])->first();

    $response = $this->json('POST', $this->url('auth/token'), [
        'client_id'  => (string) $oauthClient->getKey(),
        'grant_type' => 'internal',
        'login'      => $user->getEmail(),
        'password'   => '12345678',
    ]);

    $response->assertCreated();
    $response->assertJsonStructure($this->getAccessTokenStructure());
    $response->assertJsonPath('data.attributes.expires_in', 3600);

    $content      = $response->getDecodedContent();
    $refreshToken = data_get($content, 'data.attributes.refresh_token');

    $response = $this->json('POST', $this->url('auth/token'), [
        'client_id'     => (string) $oauthClient->getKey(),
        'grant_type'    => 'internal_refresh_token',
        'refresh_token' => $refreshToken,
    ]);

    $response->assertCreated();
    $response->assertJsonStructure($this->getAccessTokenStructure());

    $content      = $response->getDecodedContent();
    $accessToken  = data_get($content, 'data.attributes.access_token');
    $refreshToken = data_get($content, 'data.attributes.refresh_token');

    $response = $this->json('GET', $this->url('auth/me'), [], [
        'Authorization' => 'Bearer '.$accessToken,
    ]);

    $response->assertOk();
    $response->assertJsonDataItemStructure($this->getUserStructure());

    $response = $this->json('DELETE', $this->url('auth/token'), [], [
        'Authorization' => 'Bearer '.$accessToken,
    ]);
    $response->assertOk();

    $this->assertDatabaseMissing('oauth_access_tokens', [
        'user_id' => $user->id,
        'revoked' => false,
    ]);

    $response = $this->json('GET', $this->url('auth/me'), [], [
        'Authorization' => 'Bearer '.$accessToken,
    ]);

    $response->assertUnauthorized();

    $response = $this->json('POST', $this->url('auth/token'), [
        'client_id'     => (string) $oauthClient->getKey(),
        'grant_type'    => 'internal_refresh_token',
        'refresh_token' => $refreshToken,
    ]);

    $response->assertUnauthorized();
});

it('should validate token request fields before invoking passport', function (array $payload, string $field): void {
    $response = $this->json('POST', $this->url('auth/token'), $payload);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors($field);
})->with([
    'missing grant type' => [['client_id' => '00000000-0000-0000-0000-000000000000'], 'grant_type'],
    'invalid client id'  => [[
        'client_id'     => 'not-a-uuid',
        'grant_type'    => 'internal_refresh_token',
        'refresh_token' => 'token',
    ], 'client_id'],
    'invalid login'      => [[
        'client_id'  => '00000000-0000-0000-0000-000000000000',
        'grant_type' => 'internal',
        'login'      => 'not-an-email',
        'password'   => 'password',
    ], 'login'],
    'missing refresh token' => [[
        'client_id'  => '00000000-0000-0000-0000-000000000000',
        'grant_type' => 'internal_refresh_token',
    ], 'refresh_token'],
]);

it('should reject archived and unverified users', function (array $attributes): void {
    $oauthClient = ProvidesTestingData::createRandomOauthClient();
    $user        = ProvidesTestingData::createRandomUsers($attributes)->first();

    $response = $this->json('POST', $this->url('auth/token'), [
        'client_id'  => (string) $oauthClient->getKey(),
        'grant_type' => 'internal',
        'login'      => $user->getEmail(),
        'password'   => '12345678',
    ]);

    $response->assertUnauthorized();
    $response->assertErrorMessage(__('oauth.the-user-credentials-were-incorrect'));
})->with([
    'archived user'   => [['archived_at' => now()]],
    'unverified user' => [['email_verified_at' => null]],
]);

it('should reject an existing access and refresh token after the user is archived', function (): void {
    $oauthClient = ProvidesTestingData::createRandomOauthClient();
    $user        = ProvidesTestingData::createRandomUsers()->first();

    $tokenResponse = $this->json('POST', $this->url('auth/token'), [
        'client_id'  => (string) $oauthClient->getKey(),
        'grant_type' => 'internal',
        'login'      => $user->getEmail(),
        'password'   => '12345678',
    ]);
    $accessToken  = data_get($tokenResponse->getDecodedContent(), 'data.attributes.access_token');
    $refreshToken = data_get($tokenResponse->getDecodedContent(), 'data.attributes.refresh_token');

    $user->archive();

    $this->assertDatabaseMissing('oauth_access_tokens', [
        'user_id' => $user->getKey(),
        'revoked' => false,
    ]);

    $this->json('GET', $this->url('auth/me'), [], [
        'Authorization' => 'Bearer '.$accessToken,
    ])->assertUnauthorized();

    $this->json('POST', $this->url('auth/token'), [
        'client_id'     => (string) $oauthClient->getKey(),
        'grant_type'    => 'internal_refresh_token',
        'refresh_token' => $refreshToken,
    ])->assertUnauthorized();
});

it('should revoke existing tokens after a password change', function (): void {
    $oauthClient = ProvidesTestingData::createRandomOauthClient();
    $user        = ProvidesTestingData::createRandomUsers()->first();

    $tokenResponse = $this->json('POST', $this->url('auth/token'), [
        'client_id'  => (string) $oauthClient->getKey(),
        'grant_type' => 'internal',
        'login'      => $user->getEmail(),
        'password'   => '12345678',
    ]);
    $accessToken = data_get($tokenResponse->getDecodedContent(), 'data.attributes.access_token');

    $user->forceFill(['password' => 'new-secure-password'])->save();

    $this->assertDatabaseMissing('oauth_access_tokens', [
        'user_id' => $user->getKey(),
        'revoked' => false,
    ]);
    $this->json('GET', $this->url('auth/me'), [], [
        'Authorization' => 'Bearer '.$accessToken,
    ])->assertUnauthorized();
});

it('should rate limit repeated login attempts by account', function (): void {
    $oauthClient = ProvidesTestingData::createRandomOauthClient();
    $user        = ProvidesTestingData::createRandomUsers()->first();
    $payload     = [
        'client_id'  => (string) $oauthClient->getKey(),
        'grant_type' => 'internal',
        'login'      => $user->getEmail(),
        'password'   => 'wrong-password',
    ];

    foreach (range(1, 5) as $attempt) {
        $this->json('POST', $this->url('auth/token'), $payload)->assertUnauthorized();
    }

    $this->json('POST', $this->url('auth/token'), $payload)->assertTooManyRequests();
});

it('should create a public client restricted to internal grants', function (): void {
    $exitCode = Artisan::call('auth:client', ['--name' => 'Test internal client']);

    expect($exitCode)->toBe(0);

    $client = Client::query()->where('name', 'Test internal client')->firstOrFail();

    expect($client->confidential())->toBeFalse()
        ->and($client->grant_types)->toBe(['internal', 'internal_refresh_token', 'refresh_token']);
});

it('should return error with wrong password', function (): void {
    $oauthClient = ProvidesTestingData::createRandomOauthClient();
    $user        = ProvidesTestingData::createRandomUsers([
        'email' => ProvidesTestingData::getFaker()->email(),
    ])->first();

    $response = $this->json('POST', $this->url('auth/token'), [
        'client_id'  => (string) $oauthClient->getKey(),
        'grant_type' => 'internal',
        'login'      => $user->getEmail(),
        'password'   => 'wrong-password',
    ]);

    $response->assertStatus(401);
    $response->assertErrorMessage(__('oauth.the-user-credentials-were-incorrect'));
});

it('should return error with wrong email', function (): void {
    $oauthClient = ProvidesTestingData::createRandomOauthClient();
    $user        = ProvidesTestingData::createRandomUsers([
        'email' => ProvidesTestingData::getFaker()->email(),
    ])->first();

    $response = $this->json('POST', $this->url('auth/token'), [
        'client_id'  => (string) $oauthClient->getKey(),
        'grant_type' => 'internal',
        'login'      => 'wrong-'.$user->getEmail(),
        'password'   => '12345678',
    ]);

    $response->assertStatus(401);
    $response->assertErrorMessage(__('oauth.the-user-credentials-were-incorrect'));
});
