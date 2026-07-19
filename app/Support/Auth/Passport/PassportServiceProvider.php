<?php

declare(strict_types=1);

namespace App\Support\Auth\Passport;

use App\Support\Auth\Passport\Contracts\AuthServiceContract;
use App\Support\Auth\Passport\Contracts\ClientRepositoryContract;
use App\Support\Auth\Passport\Contracts\RefreshTokenBridgeRepositoryContract;
use App\Support\Auth\Passport\Contracts\RefreshTokenRepositoryContract;
use App\Support\Auth\Passport\Contracts\UserRepositoryContract;
use App\Support\Auth\Passport\Grants\InternalGrant;
use App\Support\Auth\Passport\Grants\InternalRefreshTokenGrant;
use App\Support\Auth\Passport\Repositories\ClientRepository;
use App\Support\Auth\Passport\Repositories\RefreshTokenBridgeRepository;
use App\Support\Auth\Passport\Repositories\RefreshTokenRepository;
use App\Support\Auth\Passport\Repositories\UserRepository;
use App\Support\Auth\Passport\Services\AuthService;
use DateInterval;
use Laravel\Passport\Guards\TokenGuard;
use Laravel\Passport\Passport;
use Laravel\Passport\PassportServiceProvider as BasePassportServiceProvider;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\ResourceServer;

use function tap;

class PassportServiceProvider extends BasePassportServiceProvider
{
    public function register(): void
    {
        $this->registerCustomRepositories();

        Passport::ignoreRoutes();

        parent::register();
    }

    protected function registerAuthorizationServer(): void
    {
        $this->app->singleton(AuthorizationServer::class, function () {
            return tap($this->makeAuthorizationServer(), function (AuthorizationServer $server): void {
                $accessTokenTtl = new DateInterval('PT'.max(1, (int) config('passport.access_token_ttl_minutes')).'M');

                $server->setDefaultScope(Passport::$defaultScope);
                $server->enableGrantType($this->makeInternalGrant(), $accessTokenTtl);
                $server->enableGrantType($this->makeInternalRefreshTokenGrant(), $accessTokenTtl);
            });
        });
    }

    protected function makeGuard(array $config): TokenGuard
    {
        $authManager = $this->app['auth'];

        /** @var ClientRepository $clientRepository */
        $clientRepository = $this->app->make(ClientRepositoryContract::class);

        return new TokenGuard(
            $this->app->make(ResourceServer::class),
            new ActivePassportUserProvider($authManager->createUserProvider($config['provider']), $config['provider']),
            $clientRepository,
            $this->app->make('encrypter'),
            $this->app->make('request'),
        );
    }

    protected function registerCustomRepositories(): void
    {
        $this->app->bind(ClientRepositoryContract::class, ClientRepository::class);
        $this->app->bind(RefreshTokenBridgeRepositoryContract::class, RefreshTokenBridgeRepository::class);
        $this->app->bind(UserRepositoryContract::class, UserRepository::class);
        $this->app->bind(RefreshTokenRepositoryContract::class, RefreshTokenRepository::class);
        $this->app->bind(AuthServiceContract::class, AuthService::class);
    }

    protected function makeInternalGrant(): InternalGrant
    {
        $grant = new InternalGrant(
            $this->app->make(AuthServiceContract::class),
            $this->app->make(RefreshTokenBridgeRepositoryContract::class),
        );

        $grant->setRefreshTokenTTL(
            new DateInterval('P'.max(1, (int) config('passport.refresh_token_ttl_days')).'D'),
        );

        return $grant;
    }

    protected function makeInternalRefreshTokenGrant(): InternalRefreshTokenGrant
    {
        $repository = $this->app->make(RefreshTokenBridgeRepositoryContract::class);

        $grant = new InternalRefreshTokenGrant(
            $repository,
            $this->app->make(AuthServiceContract::class),
        );
        $grant->setRefreshTokenTTL(
            new DateInterval('P'.max(1, (int) config('passport.refresh_token_ttl_days')).'D'),
        );

        return $grant;
    }
}
