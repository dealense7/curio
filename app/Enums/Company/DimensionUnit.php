<?php

declare(strict_types=1);

namespace App\Enums\Company;

use App\Enums\EnumTrait;

enum DimensionUnit: string
{
    use EnumTrait;

    case CENTIMETERS = 'cm';
    case INCHES      = 'in';
}
