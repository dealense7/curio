<?php

declare(strict_types=1);

namespace App\Enums\Company;

use App\Enums\EnumTrait;

enum WeightUnit: string
{
    use EnumTrait;

    case KILOGRAMS = 'kg';
    case POUNDS    = 'lb';
}
