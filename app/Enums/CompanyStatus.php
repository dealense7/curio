<?php

declare(strict_types=1);

namespace App\Enums;

enum CompanyStatus: string
{
    case ACTIVE    = 'active';
    case SUSPENDED = 'suspended';
    case ARCHIVED  = 'archived';
}
