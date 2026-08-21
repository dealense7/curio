<?php

declare(strict_types=1);

namespace App\Enums\Company;

use App\Enums\EnumTrait;

enum TimeFormat: string
{
    use EnumTrait;

    case TWELVE_HOUR      = '12h';
    case TWENTY_FOUR_HOUR = '24h';
}
