<?php

declare(strict_types=1);

namespace App\Enums\Company;

use App\Enums\EnumTrait;

enum DistanceUnit: string
{
    use EnumTrait;

    case KILOMETERS = 'km';
    case MILES      = 'mi';
}
