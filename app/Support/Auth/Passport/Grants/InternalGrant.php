<?php

declare(strict_types=1);

namespace App\Support\Auth\Passport\Grants;

use App\Support\Auth\Passport\Contracts\AuthServiceContract;
use App\Support\Auth\Passport\Contracts\RefreshTokenBridgeRepositoryContract;
use App\Support\Auth\Passport\Contracts\UserContract;
use DateInterval;
use Laravel\Passport\Bridge\AccessToken;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\UserEntityInterface;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Grant\AbstractGrant;
use League\OAuth2\Server\RequestEvent;
use League\OAuth2\Server\ResponseTypes\ResponseTypeInterface;
use Psr\Http\Message\ServerRequestInterface;

class InternalGrant extends AbstractGrant
{
    protected readonly AuthServiceContract $authService;

    public function __construct(
        AuthServiceContract $authService,
        RefreshTokenBridgeRepositoryContract $refreshTokenRepository,
    ) {
        $this->authService = $authService;
        $this->setRefreshTokenRepository($refreshTokenRepository);
        $this->refreshTokenTTL = new DateInterval('P1M');
    }

    public function respondToAccessTokenRequest(
        ServerRequestInterface $request,
        ResponseTypeInterface $responseType,
        DateInterval $accessTokenTTL,
    ): ResponseTypeInterface {
        $client = $this->validateClient($request);
        $scopes = $this->validateScopes($this->getRequestParameter('scope', $request));
        /** @var UserContract $user */
        $user = $this->validateUser($request);

        $scopes = $this->scopeRepository->finalizeScopes(
            $scopes,
            $this->getIdentifier(),
            $client,
            $user->getIdentifier(),
        );

        $accessToken  = $this->issueAccessToken($accessTokenTTL, $client, $user->getIdentifier(), $scopes);
        $refreshToken = $this->issueRefreshToken($accessToken);

        $responseType->setAccessToken($accessToken);
        $responseType->setRefreshToken($refreshToken);

        $this->authService->fireLoginEvent('api', $user);

        return $responseType;
    }

    public function getRefreshToken(AccessToken $token): void
    {
        $this->issueRefreshToken($token);
    }

    public function getIdentifier(): string
    {
        return 'internal';
    }

    protected function validateClient(ServerRequestInterface $request): ClientEntityInterface
    {
        $client = parent::validateClient($request);

        if (method_exists($client, 'supportsGrantType') && ! $client->supportsGrantType('refresh_token')) {
            throw OAuthServerException::unauthorizedClient();
        }

        return $client;
    }

    protected function validateUser(ServerRequestInterface $request): UserEntityInterface
    {
        $login = $this->getRequestParameter('login', $request);
        if (is_null($login)) {
            throw OAuthServerException::invalidRequest('login');
        }

        $password = $this->getRequestParameter('password', $request);
        if (is_null($password)) {
            throw OAuthServerException::invalidRequest('password');
        }

        $user = $this->authService->retrieveByCredentials(['login' => $login]);

        if (! $user instanceof UserEntityInterface) {
            $this->getEmitter()->emit(new RequestEvent(RequestEvent::USER_AUTHENTICATION_FAILED, $request));

            throw OAuthServerException::invalidCredentials();
        }

        if (! $this->authService->validateCredentials($user, (string) $password)) {
            $this->getEmitter()->emit(new RequestEvent(RequestEvent::USER_AUTHENTICATION_FAILED, $request));

            throw OAuthServerException::invalidCredentials();
        }

        return $user;
    }
}
