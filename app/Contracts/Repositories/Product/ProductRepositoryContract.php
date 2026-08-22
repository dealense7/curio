<?php

declare(strict_types=1);

namespace App\Contracts\Repositories\Product;

use App\Models\Product\Product;
use App\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface ProductRepositoryContract
{
    public function getItems(array $filters = [], array $relations = [], ?string $sort = null): Collection;

    public function getItemsWithPagination(
        array $filters = [],
        array $relations = [],
        int $page = 1,
        ?int $perPage = null,
        ?string $sort = null,
    ): LengthAwarePaginator;

    public function findByPublicId(string $publicId, array $relations = []): ?Product;

    public function create(array $data): Product;

    public function fillData(Product $product, array $data): Product;

    public function delete(Product $product): void;
}
