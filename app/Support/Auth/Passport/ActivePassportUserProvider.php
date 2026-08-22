<?php

declare(strict_types=1);

namespace App\Support\Auth\Passport;

use App\Models\User\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Laravel\Passport\PassportUserProvider;

class ActivePassportUserProvider extends PassportUserProvider
{
    public function retrieveById($identifier): ?Authenticatable
    {
        $user = parent::retrieveById($identifier);

        return $user instanceof User && $user->isEligibleForAuthentication() ? $user : null;
    }
}
