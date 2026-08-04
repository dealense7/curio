<?php

declare(strict_types=1);

namespace App\Enums\General;

use App\Enums\EnumTrait;

enum FileDisk: string
{
    use EnumTrait;

    case PUBLIC  = 'public';
    case PRIVATE = 'private';
}
