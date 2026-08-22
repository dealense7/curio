<?php

declare(strict_types=1);

namespace App\Http\Resources\Auth;

use App\Http\Resources\User\UserContactResource;
use App\Models\User\User;
use App\Support\Resources\JsonResource;
use App\Support\Resources\JsonResourceCollection;

class CurrentUserResource extends JsonResource
{
    protected static array $transformMapping = [
        'first_name'       => 'first_name',
        'last_name'        => 'last_name',
        'full_name'        => 'full_name',
        'email'            => 'email',
        'status'           => 'status',
        'preferred_locale' => 'preferred_locale',
        'created_at'       => 'created_at',
        'updated_at'       => 'updated_at',
    ];

    public function __construct(?User $resource)
    {
        $this->resource = $resource;
    }

    public function includeContacts(): JsonResourceCollection
    {
        return new JsonResourceCollection($this->whenLoaded('contacts'), UserContactResource::class);
    }
}
