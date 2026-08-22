<?php

declare(strict_types=1);

namespace App\Enums\General\File;

use App\Enums\EnumTrait;

enum Disk: int
{
    use EnumTrait;

    case PUBLIC  = 1;
    case PRIVATE = 2;
}
