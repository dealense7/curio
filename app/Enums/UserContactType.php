<?php

declare(strict_types=1);

namespace App\Enums;

enum UserContactType: string
{
    use EnumTrait;

    case PHONE   = 'phone';
    case ADDRESS = 'address';
    case EMAIL   = 'email';
}
