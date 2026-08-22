<?php

declare(strict_types=1);

namespace App\CacheRepositories\General\Category;

use App\CacheRepositories\CacheRepository;
use App\Contracts\Repositories\General\Category\CategoryRepositoryContract;
use App\Models\General\Category\Category;
use App\Repositories\General\Category\CategoryRepository;
use App\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CategoryCacheRepository extends CacheRepository implements CategoryRepositoryContract
{
    protected string $cacheKey = Category::class;

    public function __construct(private readonly CategoryRepository $repository) {}

    public function getItems(array $filters = [], array $relations = [], ?string $sort = null): Collection
    {
        return $this->remember(
            $this->createKey('items', [$filters, $relations, $sort]),
            fn (): Collection => $this->repository->getItems($filters, $relations, $sort),
        );
    }

    public function getItemsWithPagination(
        array $filters = [],
        array $relations = [],
        int $page = 1,
        ?int $perPage = null,
        ?string $sort = null,
    ): LengthAwarePaginator {
        return $this->remember(
            $this->createKey('items_paginated', [$filters, $relations, $page, $perPage, $sort]),
            fn (): LengthAwarePaginator => $this->repository->getItemsWithPagination($filters, $relations, $page, $perPage, $sort),
        );
    }

    public function findByPublicId(string $publicId, array $relations = []): ?Category
    {
        return $this->rememberNullable(
            $this->createKey('find_by_public_id', [$publicId, $relations]),
            fn (): ?Category => $this->repository->findByPublicId($publicId, $relations),
        );
    }

    public function slugExists(string $slug, ?string $exceptPublicId = null): bool
    {
        return $this->remember(
            $this->createKey('slug_exists', [$slug, $exceptPublicId]),
            fn (): bool => $this->repository->slugExists($slug, $exceptPublicId),
        );
    }

    public function create(array $data): Category
    {
        $category = $this->repository->create($data);
        $this->clear();

        return $category;
    }

    public function fillData(Category $category, array $data): Category
    {
        $category = $this->repository->fillData($category, $data);
        $this->clear();

        return $category;
    }

    public function delete(Category $category): void
    {
        $this->repository->delete($category);
        $this->clear();
    }
}
