<?php

declare(strict_types=1);

namespace App\Support\Helpers;

use App\Models\User;

class Helper
{
    public static function getUser(): ?User
    {
        /** @var ?User $user */
        $user = auth()->user();

        return $user;
    }
}
