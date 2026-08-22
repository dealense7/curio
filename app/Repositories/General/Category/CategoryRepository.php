<?php

declare(strict_types=1);

namespace App\Repositories\General\Category;

use App\Contracts\Repositories\General\Category\CategoryRepositoryContract;
use App\Filters\Admin\Category\FilterByName;
use App\Filters\Admin\Category\FilterByParentId;
use App\Filters\Admin\Category\FilterBySlug;
use App\Models\General\Category\Category;
use App\Repositories\Repository;
use App\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CategoryRepository extends Repository implements CategoryRepositoryContract
{
    public function getItems(array $filters = [], array $relations = [], ?string $sort = null): Collection
    {
        $query = $this->getData($filters)->with($relations);

        foreach ($this->getModel()->parseSort($sort) as $column => $direction) {
            $query->orderBy($column, $direction);
        }

        /** @var Collection<int, Category> $items */
        $items = $query->get();

        return $items;
    }

    public function getItemsWithPagination(
        array $filters = [],
        array $relations = [],
        int $page = 1,
        ?int $perPage = null,
        ?string $sort = null,
    ): LengthAwarePaginator {
        $query = $this->getData($filters)->with($relations);

        foreach ($this->getModel()->parseSort($sort) as $column => $direction) {
            $query->orderBy($column, $direction);
        }

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    public function findByPublicId(string $publicId, array $relations = []): ?Category
    {
        /** @var ?Category $category */
        $category = $this->getData()
            ->with($relations)
            ->where('public_id', $publicId)
            ->first();

        return $category;
    }

    public function slugExists(string $slug, ?string $exceptPublicId = null): bool
    {
        $query = $this->getData(['slug' => $slug]);

        if ($exceptPublicId !== null) {
            $query->where('public_id', '!=', $exceptPublicId);
        }

        return $query->exists();
    }

    public function create(array $data): Category
    {
        return $this->fillData($this->getModel(), $data);
    }

    public function fillData(Category $category, array $data): Category
    {
        $category->fill($data);
        $category->saveOrFail();

        return $category;
    }

    public function delete(Category $category): void
    {
        $category->delete();
    }

    public function getModel(): Category
    {
        return new Category;
    }

    public function getFilters(): array
    {
        return [
            FilterByName::class,
            FilterBySlug::class,
            FilterByParentId::class,
        ];
    }
}
