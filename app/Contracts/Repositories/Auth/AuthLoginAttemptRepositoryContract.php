<?php

declare(strict_types=1);

namespace App\Contracts\Repositories\Auth;

use App\Models\User;

interface AuthLoginAttemptRepositoryContract
{
    public function recordSuccessfulLogin(User $user, ?string $ipAddress, ?string $userAgent): void;
}
