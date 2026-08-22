<?php

declare(strict_types=1);

namespace App\Repositories\Retailer;

use App\Contracts\Repositories\Retailer\RetailerRepositoryContract;
use App\Filters\Admin\FilterByIsActive;
use App\Filters\Admin\Retailer\FilterByDomain;
use App\Filters\Admin\Retailer\FilterByName;
use App\Filters\Admin\Retailer\FilterByScrapingEnabled;
use App\Filters\Admin\Retailer\FilterBySlug;
use App\Models\Retailer\Retailer;
use App\Repositories\Repository;
use App\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class RetailerRepository extends Repository implements RetailerRepositoryContract
{
    public function getItems(array $filters = [], array $relations = [], ?string $sort = null): Collection
    {
        $query = $this->getData($filters)->with($relations);

        foreach ($this->getModel()->parseSort($sort) as $column => $direction) {
            $query->orderBy($column, $direction);
        }

        /** @var Collection<int, Retailer> $items */
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

    public function findByPublicId(string $publicId, array $relations = []): ?Retailer
    {
        /** @var ?Retailer $retailer */
        $retailer = $this->getData()
            ->with($relations)
            ->where('public_id', $publicId)
            ->first();

        return $retailer;
    }

    public function slugExists(string $slug, ?string $exceptPublicId = null): bool
    {
        $query = $this->getData(['slug' => $slug]);

        if ($exceptPublicId !== null) {
            $query->where('public_id', '!=', $exceptPublicId);
        }

        return $query->exists();
    }

    public function create(array $data): Retailer
    {
        return $this->fillData($this->getModel(), $data);
    }

    public function fillData(Retailer $retailer, array $data): Retailer
    {
        $retailer->fill($data);
        $retailer->forceFill(array_intersect_key($data, array_flip(['created_by', 'updated_by'])));
        $retailer->saveOrFail();

        return $retailer;
    }

    public function delete(Retailer $retailer): void
    {
        $retailer->delete();
    }

    public function getModel(): Retailer
    {
        return new Retailer;
    }

    public function getFilters(): array
    {
        return [
            FilterByName::class,
            FilterBySlug::class,
            FilterByDomain::class,
            FilterByIsActive::class,
            FilterByScrapingEnabled::class,
        ];
    }
}
