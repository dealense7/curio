<?php

declare(strict_types=1);

namespace App\Http\Resources\Auth;

use App\Models\User;
use App\Support\Resources\JsonResource;

class CurrentUserResource extends JsonResource
{
    protected static array $transformMapping = [
        'name'       => 'name',
        'email'      => 'email',
        'created_at' => 'created_at',
        'updated_at' => 'updated_at',
    ];

    public function __construct(?User $resource)
    {
        $this->resource = $resource;
    }
}
