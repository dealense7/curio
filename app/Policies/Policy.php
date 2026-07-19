<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Auth\Access\Response;

class Policy
{
    protected function allow(): Response
    {
        return Response::allow();
    }

    protected function denyWithMessage(string $permissionName): Response
    {
        return Response::deny(__('policy.permission_denied', ['permission' => $permissionName]), 403);
    }

    protected function denyWithCustomMessage(string $message): Response
    {
        return Response::deny($message, 403);
    }
}
