<?php

declare(strict_types=1);

namespace App\Support\Testing;

use Illuminate\Support\Str;
use InvalidArgumentException;

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

    private array $difficultyStructure = [
        'type',
        'id',
        'attributes' => [
            'key',
            'display_name',
        ],
    ];

    private array $currencyStructure = [
        'type',
        'id',
        'attributes' => [
            'code',
            'name',
            'symbol',
            'decimal_places',
            'is_active',
            'sort_order',
        ],
    ];

    private array $publishingStatusStructure = [
        'type',
        'id',
        'attributes' => [
            'key',
            'display_name',
        ],
    ];

    private array $monthStructure = [
        'type',
        'id',
        'attributes' => [
            'key',
            'display_name',
            'sort_order',
        ],
    ];

    private array $fileStructure = [
        'type',
        'id',
        'attributes' => [
            'original_name',
            'name',
            'folder',
            'extension',
            'mime',
            'size',
            'disk',
            'type',
            'status',
            'created_at',
            'updated_at',
        ],
    ];

    private array $tourStructure = [
        'type',
        'id',
        'attributes' => [
            'title',
            'description',
            'start_location',
            'end_location',
            'distance_km',
            'duration_comfortable_days',
            'duration_recommended_days',
            'daily_distance_comfortable_km',
            'daily_distance_recommended_km',
            'elevation_gain_m',
            'price_comfortable',
            'price_recommended',
            'average_daily_spend',
            'created_at',
            'updated_at',
        ],
    ];

    private array $companyStructure = [
        'type',
        'id',
        'attributes' => [
            'display_name',
            'legal_name',
            'slug',
            'status',
            'country_id',
            'default_currency_id',
            'timezone',
            'default_locale',
            'support_email',
            'support_phone',
            'website_url',
            'logo_path',
            'suspended_at',
            'suspension_reason',
            'created_at',
            'updated_at',
            'archived_at',
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

    public function getDifficultyStructure(): array
    {
        return $this->difficultyStructure;
    }

    public function getCurrencyStructure(): array
    {
        return $this->currencyStructure;
    }

    public function getPublishingStatusStructure(): array
    {
        return $this->publishingStatusStructure;
    }

    public function getMonthStructure(): array
    {
        return $this->monthStructure;
    }

    public function getFileStructure(): array
    {
        return $this->fileStructure;
    }

    public function getTourStructure(array $relations = []): array
    {
        $structure = $this->tourStructure;

        $this->includeNestedRelations($structure, $relations);

        return $structure;
    }

    public function getCompanyStructure(): array
    {
        return $this->companyStructure;
    }

    public function getTourConfigStructure(): array
    {
        return [
            'difficulties'        => [$this->difficultyStructure],
            'publishing_statuses' => [$this->publishingStatusStructure],
            'currencies'          => [$this->currencyStructure],
            'months'              => [$this->monthStructure],
        ];
    }

    protected function includeNestedRelations(array &$item, array $relations): void
    {
        foreach ($relations as $relation) {
            $parts = explode('.', $relation);
            $this->includeNestedRelation($item, $parts);
        }
    }

    protected function includeNestedRelation(array &$item, array $parentRelations): void
    {
        $currentRelation = array_shift($parentRelations);

        if ($parentRelations !== []) {
            $isCollection = Str::startsWith($currentRelation, '[');
            $relationKey  = trim($currentRelation, '[]');
            if ($isCollection) {
                $this->includeNestedRelation($item['relationships'][$relationKey]['data'][0], $parentRelations);
            } else {
                $this->includeNestedRelation($item['relationships'][$relationKey]['data'], $parentRelations);
            }

            return;
        }

        $isCollection                 = Str::startsWith($currentRelation, '[');
        $currentRelation              = trim($currentRelation, '[]');
        [$relationKey, $relationItem] = Str::contains($currentRelation, ':')
            ? explode(':', $currentRelation, 2)
            : [$currentRelation, $currentRelation];
        $property = Str::camel($relationItem).'Structure';

        if (! property_exists($this, $property)) {
            throw new InvalidArgumentException('Relation structure for '.$relationItem.' does not exist');
        }

        $item['relationships'][$relationKey]['data'] = $isCollection
            ? [$this->{$property}]
            : $this->{$property};
    }
}
