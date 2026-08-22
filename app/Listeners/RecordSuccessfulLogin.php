<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Contracts\Repositories\Auth\AuthLoginAttemptRepositoryContract;
use App\Models\User\User;
use App\Support\Auth\Passport\Contracts\UserRepositoryContract;
use Illuminate\Auth\Events\Login;
use Illuminate\Http\Request;

class RecordSuccessfulLogin
{
    public function __construct(
        private readonly UserRepositoryContract $userRepository,
        private readonly AuthLoginAttemptRepositoryContract $loginAttemptRepository,
        private readonly Request $request,
    ) {}

    public function handle(Login $event): void
    {
        if ($event->guard !== 'api') {
            return;
        }

        $user = $event->user;
        if (! $user instanceof User) {
            return;
        }

        $ipAddress = $this->request->ip();
        $this->userRepository->recordLogin($user, $ipAddress);
        $this->loginAttemptRepository->recordSuccessfulLogin($user, $ipAddress, $this->request->userAgent());
    }
}
