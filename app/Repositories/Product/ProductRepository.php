<?php

declare(strict_types=1);

namespace App\Repositories\Product;

use App\Contracts\Repositories\Product\ProductRepositoryContract;
use App\Filters\Admin\FilterByIsActive;
use App\Filters\Admin\Product\FilterByBrand;
use App\Filters\Admin\Product\FilterByCategoryId;
use App\Filters\Admin\Product\FilterByGtin;
use App\Filters\Admin\Product\FilterByName;
use App\Models\Product\Product;
use App\Repositories\Repository;
use App\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductRepository extends Repository implements ProductRepositoryContract
{
    public function getItems(array $filters = [], array $relations = [], ?string $sort = null): Collection
    {
        $query = $this->getData($filters)->with($relations);
        foreach ($this->getModel()->parseSort($sort) as $column => $direction) {
            $query->orderBy($column, $direction);
        }
        /** @var Collection<int, Product> $items */
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

    public function findByPublicId(string $publicId, array $relations = []): ?Product
    {
        /** @var ?Product $product */
        $product = $this->getData()->with($relations)->where('public_id', $publicId)->first();

        return $product;
    }

    public function create(array $data): Product
    {
        return $this->fillData($this->getModel(), $data);
    }

    public function fillData(Product $product, array $data): Product
    {
        $product->fill($data);
        $product->saveOrFail();

        return $product;
    }

    public function delete(Product $product): void
    {
        $product->delete();
    }

    public function getModel(): Product
    {
        return new Product;
    }

    public function getFilters(): array
    {
        return [
            FilterByName::class,
            FilterByBrand::class,
            FilterByGtin::class,
            FilterByIsActive::class,
            FilterByCategoryId::class,
        ];
    }
}
