<?php

declare(strict_types=1);

namespace App\Support\Auth\Passport\Services;

use App\Support\Auth\Passport\Contracts\AuthServiceContract;
use App\Support\Auth\Passport\Contracts\RefreshTokenRepositoryContract;
use App\Support\Auth\Passport\Contracts\UserContract;
use App\Support\Auth\Passport\Contracts\UserRepositoryContract;
use Illuminate\Auth\AuthManager;
use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;
use InvalidArgumentException;

use function is_null;

use Laravel\Passport\Guards\TokenGuard;
use Laravel\Passport\Token;

class AuthService implements AuthServiceContract
{
    public function __construct(
        protected RefreshTokenRepositoryContract $refreshTokenRepositoryContract,
        protected UserRepositoryContract $userRepositoryContract,
        protected AuthManager $authManager,
        protected HasherContract $hasher,
        protected Dispatcher $dispatcher,
    ) {}

    public function findOneForAuth(int $id): ?UserContract
    {
        return $this->userRepositoryContract->findOneForAuth($id);
    }

    public function retrieveUserById(int $id): ?UserContract
    {
        return $this->findOneForAuth($id);
    }

    public function updateRememberToken(UserContract $user, string $token): void
    {
        $this->userRepositoryContract->updateRememberToken($user, $token);
    }

    public function revokeToken(): void
    {
        if (! $this->authManager->guard('api') instanceof TokenGuard) {
            throw new InvalidArgumentException('Current guard is not request guard');
        }

        /** @var UserContract|null $user */
        $user = $this->authManager->guard('api')->user();
        if ($user === null) {
            return;
        }

        /** @var Token|null $token */
        $token = $user->token();
        if (is_null($token)) {
            return;
        }

        $tokenId = (string) $token->getKey();

        $token->revoke();
        $this->refreshTokenRepositoryContract->revokeRefreshTokensByAccessTokenId($tokenId);
        $this->unsetUser();
    }

    public function revokeOtherTokens(): void
    {
        if (! $this->authManager->guard('api') instanceof TokenGuard) {
            throw new InvalidArgumentException('Current guard is not request guard');
        }

        /** @var UserContract|null $user */
        $user = $this->authManager->guard('api')->user();
        if ($user === null || ! isset($user->tokens)) {
            return;
        }

        /** @var Token $currentToken */
        $currentToken = $user->token();

        /** @var Token $token */
        foreach ($user->tokens as $token) {
            if ($currentToken->getKey() === $token->getKey()) {
                continue;
            }

            $tokenId = (string) $token->getKey();
            $token->revoke();
            $this->refreshTokenRepositoryContract->revokeRefreshTokensByAccessTokenId($tokenId);
        }
    }

    public function unsetUser(): void
    {
        /** @var TokenGuard $guard */
        $guard = $this->authManager->guard('api');
        $guard->forgetUser();
    }

    public function getUser(array $relationships = []): ?UserContract
    {
        /** @var UserContract|null $user */
        $user = $this->authManager->guard('api')->user();
        if ($user === null) {
            return null;
        }

        return $this->userRepositoryContract->getById((int) $user->getAuthIdentifier(), $relationships);
    }

    public function retrieveByCredentials(array $credentials): ?UserContract
    {
        return $this->userRepositoryContract->retrieveByCredentials($credentials);
    }

    public function retrieveUserByToken(int $identifier, string $token): ?UserContract
    {
        return $this->userRepositoryContract->retrieveUserByToken($identifier, $token);
    }

    public function validateCredentials(UserContract $user, string $password): bool
    {
        return $this->hasher->check($password, $user->getAuthPassword());
    }

    public function fireLoginEvent(string $guard, UserContract $user, bool $remember = false): void
    {
        $this->dispatcher->dispatch(new Login($guard, $user, $remember));
    }
}
