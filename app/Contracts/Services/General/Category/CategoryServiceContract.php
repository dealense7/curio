<?php

declare(strict_types=1);

namespace App\Contracts\Services\General\Category;

use App\Models\General\Category\Category;
use App\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface CategoryServiceContract
{
    public function getItems(array $filters = [], array $relations = [], ?string $sort = null): Collection;

    public function getItemsWithPagination(
        array $filters = [],
        array $relations = [],
        int $page = 1,
        ?int $perPage = null,
        ?string $sort = null,
    ): LengthAwarePaginator;

    public function findByPublicId(string $publicId, array $relations = [], bool $checkPermission = true): ?Category;

    public function slugExists(string $slug, ?string $exceptPublicId = null): bool;

    public function create(array $data): Category;

    public function update(Category $category, array $data): Category;

    public function delete(Category $category): void;
}
