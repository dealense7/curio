<?php

declare(strict_types=1);

namespace App\Enums\General;

use App\Enums\EnumTrait;

enum FileStatus: string
{
    use EnumTrait;

    case TEMPORARY = 'temporary';
    case CONFIRMED = 'confirmed';
}
