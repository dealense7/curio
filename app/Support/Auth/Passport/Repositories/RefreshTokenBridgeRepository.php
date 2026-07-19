<?php

declare(strict_types=1);

namespace App\Support\Auth\Passport\Repositories;

use App\Support\Auth\Passport\Contracts\RefreshTokenBridgeRepositoryContract;
use Laravel\Passport\Bridge\RefreshTokenRepository as BaseRefreshTokenRepository;

class RefreshTokenBridgeRepository extends BaseRefreshTokenRepository implements RefreshTokenBridgeRepositoryContract
{
    //
}
