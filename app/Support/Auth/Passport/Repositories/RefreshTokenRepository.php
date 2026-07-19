<?php

declare(strict_types=1);

namespace App\Support\Auth\Passport\Repositories;

use App\Support\Auth\Passport\Contracts\RefreshTokenRepositoryContract;
use Laravel\Passport\Bridge\RefreshTokenRepository as BaseRefreshTokenRepository;
use Laravel\Passport\Passport;

class RefreshTokenRepository extends BaseRefreshTokenRepository implements RefreshTokenRepositoryContract
{
    public function revokeRefreshTokensByAccessTokenId(string $accessTokenId): void
    {
        Passport::refreshToken()
            ->newQuery()
            ->where('access_token_id', $accessTokenId)
            ->update(['revoked' => true]);
    }
}
