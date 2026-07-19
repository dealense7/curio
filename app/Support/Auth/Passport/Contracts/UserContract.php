<?php

declare(strict_types=1);

namespace App\Support\Auth\Passport\Contracts;

use Illuminate\Contracts\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\CanResetPassword;
use Laravel\Passport\Contracts\OAuthenticatable;
use League\OAuth2\Server\Entities\UserEntityInterface;

/**
 * @method \Laravel\Passport\Token|null token()
 */
interface UserContract extends Authenticatable, Authorizable, CanResetPassword, OAuthenticatable, UserEntityInterface
{
    //
}
