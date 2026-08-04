<?php

declare(strict_types=1);

namespace App\Enums\General;

use App\Enums\EnumTrait;

enum FileType: string
{
    use EnumTrait;

    case GENERAL = 'general';
    case ROUTE   = 'route';
    case IMAGE   = 'image';
}
