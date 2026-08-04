<?php

declare(strict_types=1);

namespace App\Enums\General;

use App\Enums\EnumTrait;

enum Difficulty: string
{
    use EnumTrait;

    case EASY        = 'easy';
    case MODERATE    = 'moderate';
    case CHALLENGING = 'challenging';
    case DIFFICULT   = 'difficult';

    public function getText(): string
    {
        return ucfirst($this->value);
    }
}
