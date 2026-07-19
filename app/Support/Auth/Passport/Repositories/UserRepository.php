<?php

declare(strict_types=1);

namespace App\Support\Auth\Passport\Repositories;

use App\Models\User;
use App\Support\Auth\Passport\Contracts\UserContract;
use App\Support\Auth\Passport\Contracts\UserRepositoryContract;
use Exception;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\UserEntityInterface;
use RuntimeException;

class UserRepository implements UserRepositoryContract
{
    public function findOneForAuth(int $id): ?UserContract
    {
        return $this->getById($id);
    }

    public function getUserEntityByUserCredentials(
        string $username,
        string $password,
        string $grantType,
        ClientEntityInterface $clientEntity,
    ): ?UserEntityInterface {
        throw new Exception('getUserEntityByUserCredentials is deprecated!');
    }

    public function retrieveByCredentials(array $credentials): ?UserContract
    {
        if (empty($credentials)) {
            return null;
        }

        $login = (string) ($credentials['login'] ?? $credentials['email'] ?? '');
        if ($login === '') {
            return null;
        }

        /** @var UserContract|null $user */
        $user = $this->getModel()
            ->newQuery()
            ->where('email', '=', $login)
            ->whereNull('archived_at')
            ->whereNotNull('email_verified_at')
            ->first();

        return $user;
    }

    public function getById(int $id, array $relationships = []): ?UserContract
    {
        /** @var UserContract|null $user */
        $user = $this->getModel()
            ->newQuery()
            ->where('id', $id)
            ->whereNull('archived_at')
            ->whereNotNull('email_verified_at')
            ->with($relationships)
            ->first();

        return $user;
    }

    public function retrieveUserByToken(int $identifier, string $token): ?UserContract
    {
        throw new RuntimeException('Not implemented');
    }

    public function updateRememberToken(UserContract $user, string $token): void
    {
        // Not used for API token auth.
    }

    protected function getModel(): User
    {
        return new User;
    }
}
