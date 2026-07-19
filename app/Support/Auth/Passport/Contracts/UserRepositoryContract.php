<?php

declare(strict_types=1);

namespace App\Support\Auth\Passport\Contracts;

use League\OAuth2\Server\Repositories\UserRepositoryInterface;

interface UserRepositoryContract extends UserRepositoryInterface
{
    public function findOneForAuth(int $id): ?UserContract;

    public function retrieveByCredentials(array $credentials): ?UserContract;

    public function getById(int $id, array $relationships = []): ?UserContract;

    public function retrieveUserByToken(int $identifier, string $token): ?UserContract;

    public function updateRememberToken(UserContract $user, string $token): void;
}
