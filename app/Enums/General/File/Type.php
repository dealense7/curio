<?php

declare(strict_types=1);

namespace App\Enums\General\File;

use App\Enums\EnumTrait;

enum Type: int
{
    use EnumTrait;

    case GENERAL          = 1;
    case INSPECTION_IMAGE = 2;
    case INSPECTION_FILE  = 3;
    case USER_CERTIFICATE = 4;
    case USER_SIGNATURE   = 5;
    case ANALOGY_IMAGE    = 6;
    case NAPR_FILE        = 7;
    case VACANT_LAND_FILE = 8;
}
