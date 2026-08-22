<?php

declare(strict_types=1);

namespace App\Repositories\Auth;

use App\Contracts\Repositories\Auth\AuthLoginAttemptRepositoryContract;
use App\Models\Auth\AuthLoginAttempt;
use App\Models\User\User;
use App\Repositories\Repository;
use Illuminate\Support\Carbon;

class AuthLoginAttemptRepository extends Repository implements AuthLoginAttemptRepositoryContract
{
    public function recordSuccessfulLogin(User $user, ?string $ipAddress, ?string $userAgent): void
    {
        $attempt = $this->getModel();
        $attempt->fill([
            'user_id'      => $user->getKey(),
            'login'        => $user->getEmail(),
            'succeeded'    => true,
            'ip_address'   => $ipAddress,
            'user_agent'   => $userAgent,
            'attempted_at' => Carbon::now('UTC'),
        ]);
        $attempt->saveOrFail();
    }

    public function getModel(): AuthLoginAttempt
    {
        return new AuthLoginAttempt;
    }
}
