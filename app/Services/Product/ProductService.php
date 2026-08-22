<?php

declare(strict_types=1);

namespace App\Services\Product;

use App\Contracts\Repositories\Product\ProductRepositoryContract;
use App\Contracts\Services\Product\ProductServiceContract;
use App\Models\Product\Product;
use App\Services\Service;
use App\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ProductService extends Service implements ProductServiceContract
{
    public function __construct(private readonly ProductRepositoryContract $repository) {}

    public function getItems(array $filters = [], array $relations = [], ?string $sort = null): Collection
    {
        $this->authorize('read', new Product);

        return $this->repository->getItems($filters, $relations, $sort);
    }

    public function getItemsWithPagination(
        array $filters = [],
        array $relations = [],
        int $page = 1,
        ?int $perPage = null,
        ?string $sort = null,
    ): LengthAwarePaginator {
        $this->authorize('read', new Product);

        return $this->repository->getItemsWithPagination($filters, $relations, $page, $perPage, $sort);
    }

    public function findByPublicId(string $publicId, array $relations = []): ?Product
    {
        $product = $this->repository->findByPublicId($publicId, $relations);

        if ($product !== null) {
            $this->authorize('read', $product);
        }

        return $product;
    }

    public function create(array $data): Product
    {
        $this->authorize('create', new Product);

        return DB::transaction(
            fn (): Product => $this->repository->create($data),
        );
    }

    public function update(Product $product, array $data): Product
    {
        $this->authorize('update', $product);

        return DB::transaction(
            fn (): Product => $this->repository->fillData($product, $data),
        );
    }

    public function delete(Product $product): void
    {
        $this->authorize('delete', $product);
        DB::transaction(function () use ($product): void {
            $this->repository->delete($product);
        });
    }
}
