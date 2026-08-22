<?php

declare(strict_types=1);

namespace App\Enums\User;

use App\Enums\EnumTrait;

enum UserContactType: string
{
    use EnumTrait;

    case PHONE   = 'phone';
    case ADDRESS = 'address';
    case EMAIL   = 'email';
}
