<?php

declare(strict_types=1);

namespace App\Http\Resources\User;

use App\Http\Resources\JsonResource;
use App\Models\User\User;
use Illuminate\Http\Request;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var User $user */
        $user = $this->resource;

        return [
            'public_id'        => $this->publicId(),
            'first_name'       => $user->getFirstName(),
            'last_name'        => $user->getLastName(),
            'full_name'        => $user->getFullName(),
            'email'            => $user->getEmail(),
            'status'           => $user->getStatus(),
            'preferred_locale' => $user->getAttribute('preferred_locale'),
            'created_at'       => $this->dateTime($user->getCreatedAt()),
            'updated_at'       => $this->dateTime($user->getUpdatedAt()),
        ];
    }
}
