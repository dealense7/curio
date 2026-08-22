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

    private array $userContactStructure = [
        'type',
        'id',
        'attributes' => [
            'public_id',
            'type',
            'label',
            'value',
            'is_primary',
        ],
    ];

    private array $userStructure = [
        'type',
        'id',
        'attributes' => [
            'first_name',
            'last_name',
            'full_name',
            'email',
            'status',
            'preferred_locale',
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

    private array $retailerStructure = [
        'type',
        'id',
        'attributes' => [
            'name',
            'slug',
            'domain',
            'country_id',
            'currency_id',
            'is_active',
            'scraping_enabled',
            'last_scraped_at',
            'created_at',
            'updated_at',
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

    public function getUserStructure(array $relations = []): array
    {
        $structure = $this->userStructure;

        $this->includeNestedRelations($structure, $relations);

        return $structure;
    }

    public function getAclStructure(): array
    {
        return $this->aclStructure;
    }

    public function getCurrencyStructure(): array
    {
        return $this->currencyStructure;
    }

    public function getRetailerStructure(array $relations = []): array
    {
        $structure = $this->retailerStructure;

        $this->includeNestedRelations($structure, $relations);

        return $structure;
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
