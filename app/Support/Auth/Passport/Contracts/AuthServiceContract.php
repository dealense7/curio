<?php

declare(strict_types=1);

namespace App\Support\Auth\Passport\Contracts;

interface AuthServiceContract
{
    public function findOneForAuth(int $id): ?UserContract;

    public function retrieveUserById(int $id): ?UserContract;

    public function updateRememberToken(UserContract $user, string $token): void;

    public function revokeToken(): void;

    public function revokeOtherTokens(): void;

    public function unsetUser(): void;

    public function getUser(array $relationships = []): ?UserContract;

    public function retrieveByCredentials(array $credentials): ?UserContract;

    public function retrieveUserByToken(int $identifier, string $token): ?UserContract;

    public function validateCredentials(UserContract $user, string $password): bool;

    public function fireLoginEvent(string $guard, UserContract $user, bool $remember = false): void;
}
