<?php

declare(strict_types=1);

namespace App\Http\Resources\User;

use App\Models\User\UserContact;
use App\Support\Resources\JsonResource;

class UserContactResource extends JsonResource
{
    protected static array $transformMapping = [
        'public_id'  => 'public_id',
        'type'       => 'type',
        'label'      => 'label',
        'value'      => 'value',
        'is_primary' => 'isPrimary',
    ];

    public function __construct(?UserContact $resource)
    {
        $this->resource = $resource;
    }
}
