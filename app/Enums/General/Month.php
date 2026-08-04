<?php

declare(strict_types=1);

namespace App\Enums\General;

use App\Enums\EnumTrait;

enum Month: string
{
    use EnumTrait;

    case JANUARY   = 'january';
    case FEBRUARY  = 'february';
    case MARCH     = 'march';
    case APRIL     = 'april';
    case MAY       = 'may';
    case JUNE      = 'june';
    case JULY      = 'july';
    case AUGUST    = 'august';
    case SEPTEMBER = 'september';
    case OCTOBER   = 'october';
    case NOVEMBER  = 'november';
    case DECEMBER  = 'december';

    public function getText(): string
    {
        return ucfirst($this->value);
    }
}
