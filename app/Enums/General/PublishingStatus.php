<?php

declare(strict_types=1);

namespace App\Enums\General;

use App\Enums\EnumTrait;

enum PublishingStatus: string
{
    use EnumTrait;

    case PUBLISHED = 'published';
    case PRIVATE   = 'private';

    public function getText(): string
    {
        return ucfirst($this->value);
    }
}
