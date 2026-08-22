<?php

declare(strict_types=1);

namespace App\Enums\General\File;

use App\Enums\EnumTrait;

enum Status: int
{
    use EnumTrait;

    case UNCONFIRMED  = 0;
    case CONFIRMED    = 1;
    case TEMPORARY    = 2;
    case TRANSFERRING = 3;
}
