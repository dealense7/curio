<?php

declare(strict_types=1);

namespace App\Support\Testing;

trait ProvidesItemStructures
{
    private array $successStructure = [
        'message',
        'data',
    ];

    private array $errorStructure = [
        'message',
        'errors',
    ];

    private array $accessTokenStructure = [
        'data' => [
            'id',
            'type',
            'attributes' => [
                'token_type',
                'expires_in',
                'access_token',
                'refresh_token',
            ],
        ],
    ];

    private array $userStructure = [
        'type',
        'id',
        'attributes' => [
            'name',
            'email',
            'created_at',
            'updated_at',
        ],
    ];

    private array $aclStructure = [
        'type',
        'id',
        'attributes' => [
            'permissions',
            'roles',
        ],
    ];

    public function getSuccessStructure(): array
    {
        return $this->successStructure;
    }

    public function getErrorStructure(): array
    {
        return $this->errorStructure;
    }

    public function getAccessTokenStructure(): array
    {
        return $this->accessTokenStructure;
    }

    public function getUserStructure(): array
    {
        return $this->userStructure;
    }

    public function getAclStructure(): array
    {
        return $this->aclStructure;
    }
}
