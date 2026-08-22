<?php

declare(strict_types=1);

namespace App\CacheRepositories\Product;

use App\CacheRepositories\CacheRepository;
use App\Contracts\Repositories\Product\ProductRepositoryContract;
use App\Models\Product\Product;
use App\Repositories\Product\ProductRepository;
use App\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductCacheRepository extends CacheRepository implements ProductRepositoryContract
{
    protected string $cacheKey = Product::class;

    public function __construct(private readonly ProductRepository $repository)
    {
        //
    }

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
        return $this->repository->getItemsWithPagination($filters, $relations, $page, $perPage, $sort);
    }

    public function findByPublicId(string $publicId, array $relations = []): ?Product
    {
        return $this->rememberNullable(
            $this->createKey('find_by_public_id', [$publicId, $relations]),
            fn (): ?Product => $this->repository->findByPublicId($publicId, $relations),
        );
    }

    public function create(array $data): Product
    {
        $product = $this->repository->create($data);
        $this->clear();

        return $product;
    }

    public function fillData(Product $product, array $data): Product
    {
        $product = $this->repository->fillData($product, $data);
        $this->clear();

        return $product;
    }

    public function delete(Product $product): void
    {
        $this->repository->delete($product);
        $this->clear();
    }
}
