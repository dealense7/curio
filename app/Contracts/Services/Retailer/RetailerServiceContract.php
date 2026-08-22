<?php

declare(strict_types=1);

namespace App\Contracts\Services\Retailer;

use App\Models\Retailer\Retailer;
use App\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface RetailerServiceContract
{
    public function getItems(array $filters = [], array $relations = []): Collection;

    public function getItemsWithPagination(
        array $filters = [],
        array $relations = [],
        int $page = 1,
        ?int $perPage = null,
        ?string $sort = null,
    ): LengthAwarePaginator;

    public function findByPublicId(string $publicId, array $relations = []): ?Retailer;

    public function slugExists(string $slug, ?string $exceptPublicId = null): bool;

    public function create(array $data): Retailer;

    public function update(Retailer $retailer, array $data): Retailer;

    public function delete(Retailer $retailer): void;
}
