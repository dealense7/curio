<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var User $user */
        $user = $this->resource;

        return [
            'public_id'  => $this->publicId(),
            'name'       => $user->getName(),
            'email'      => $user->getEmail(),
            'created_at' => $this->dateTime($user->getCreatedAt()),
            'updated_at' => $this->dateTime($user->getUpdatedAt()),
        ];
    }
}
