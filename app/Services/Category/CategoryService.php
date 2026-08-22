<?php

declare(strict_types=1);

namespace App\Services\Category;

use App\Contracts\Repositories\Category\CategoryRepositoryContract;
use App\Contracts\Services\Category\CategoryServiceContract;
use App\Models\Category\Category;
use App\Services\Service;
use App\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class CategoryService extends Service implements CategoryServiceContract
{
    public function __construct(private readonly CategoryRepositoryContract $repository) {}

    public function getItems(array $filters = [], array $relations = [], ?string $sort = null): Collection
    {
        $this->authorize('read', new Category);

        return $this->repository->getItems($filters, $relations, $sort);
    }

    public function getItemsWithPagination(
        array $filters = [],
        array $relations = [],
        int $page = 1,
        ?int $perPage = null,
        ?string $sort = null,
    ): LengthAwarePaginator {
        $this->authorize('read', new Category);

        return $this->repository->getItemsWithPagination($filters, $relations, $page, $perPage, $sort);
    }

    public function findByPublicId(string $publicId, array $relations = [], bool $checkPermission = true): ?Category
    {
        $category = $this->repository->findByPublicId($publicId, $relations);

        if ($category === null) {
            return null;
        }

        if ($checkPermission) {
            $this->authorize('read', $category);
        }

        return $category;
    }

    public function slugExists(string $slug, ?string $exceptPublicId = null): bool
    {
        return $this->repository->slugExists($slug, $exceptPublicId);
    }

    public function create(array $data): Category
    {
        $this->authorize('create', new Category);

        return DB::transaction(fn (): Category => $this->repository->create($data));
    }

    public function update(Category $category, array $data): Category
    {
        $this->authorize('update', $category);

        return DB::transaction(fn (): Category => $this->repository->fillData($category, $data));
    }

    public function delete(Category $category): void
    {
        $this->authorize('delete', $category);

        DB::transaction(function () use ($category): void {
            $this->repository->delete($category);
        });
    }
}
