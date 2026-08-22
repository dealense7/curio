<?php

declare(strict_types=1);

namespace App\Support\Traits;

use function array_unique;
use function explode;
use function in_array;
use function ltrim;
use function str_starts_with;
use function trim;

trait Sortable
{
    /**
     * @var list<string>|array<string, string>
     */
    protected array $sortFields = [];

    /**
     * @var array<string, string>
     */
    protected array $sortBy = ['id' => 'desc'];

    /**
     * @return list<string>|array<string, string>
     */
    public function getSortFields(): array
    {
        return array_unique(array_merge(['id'], $this->sortFields));
    }

    /**
     * @return array<string, string>
     */
    public function parseSort(?string $sort = null): array
    {
        $sorts = [];

        foreach (explode(',', (string) $sort) as $requestedSort) {
            $direction = str_starts_with(trim($requestedSort), '-') ? 'desc' : 'asc';
            $field     = ltrim(trim($requestedSort), '+-');
            $field     = $this->getRealSortField($field);

            if ($field !== null) {
                $sorts[$field] = $direction;
            }
        }

        return $sorts ?: $this->sortBy;
    }

    protected function getRealSortField(string $field): ?string
    {
        $sortFields = $this->getSortFields();

        if (isset($sortFields[$field])) {
            return $sortFields[$field];
        }

        return in_array($field, $sortFields, true) ? $field : null;
    }
}
