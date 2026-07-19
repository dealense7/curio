<?php

declare(strict_types=1);

namespace App\Http\Resources\Auth;

use App\Support\Resources\Responses\ArrayResource;

class AuthTokenResource extends ArrayResource
{
    protected static array $transformMapping = [
        'token_type'    => 'tokenType',
        'access_token'  => 'accessToken',
        'refresh_token' => 'refreshToken',
        'expires_in'    => 'expiresIn',
    ];

    protected function getResourceType(): string
    {
        return 'AuthObject';
    }
}
