<?php

declare(strict_types=1);

namespace App\Enums\User;

use App\Enums\EnumTrait;

enum UserStatus: string
{
    use EnumTrait;

    case ACTIVE    = 'active';
    case INVITED   = 'invited';
    case SUSPENDED = 'suspended';
}
