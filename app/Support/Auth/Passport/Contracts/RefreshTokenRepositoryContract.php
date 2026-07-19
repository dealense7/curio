<?php

declare(strict_types=1);

namespace App\Support\Auth\Passport\Contracts;

interface RefreshTokenRepositoryContract
{
    public function revokeRefreshToken(string $tokenId): void;

    public function revokeRefreshTokensByAccessTokenId(string $accessTokenId): void;
}
