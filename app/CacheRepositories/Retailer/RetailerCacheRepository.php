<?php

declare(strict_types=1);

namespace App\CacheRepositories\Retailer;

use App\CacheRepositories\CacheRepository;
use App\Contracts\Repositories\Retailer\RetailerRepositoryContract;
use App\Models\Retailer\Retailer;
use App\Repositories\Retailer\RetailerRepository;
use App\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class RetailerCacheRepository extends CacheRepository implements RetailerRepositoryContract
{
    protected string $cacheKey = Retailer::class;

    public function __construct(private readonly RetailerRepository $repository)
    {
        //
    }

    public function getItems(array $filters = [], array $relations = []): Collection
    {
        return $this->remember(
            $this->createKey('items', [$filters, $relations]),
            fn (): Collection => $this->repository->getItems($filters, $relations),
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

    public function findByPublicId(string $publicId, array $relations = []): ?Retailer
    {
        return $this->rememberNullable(
            $this->createKey('find_by_public_id', [$publicId, $relations]),
            fn (): ?Retailer => $this->repository->findByPublicId($publicId, $relations),
        );
    }

    public function slugExists(string $slug, ?string $exceptPublicId = null): bool
    {
        return $this->remember(
            $this->createKey('slug_exists', [$slug, $exceptPublicId]),
            fn (): bool => $this->repository->slugExists($slug, $exceptPublicId),
        );
    }

    public function create(array $data): Retailer
    {
        $retailer = $this->repository->create($data);
        $this->clear();

        return $retailer;
    }

    public function fillData(Retailer $retailer, array $data): Retailer
    {
        $retailer = $this->repository->fillData($retailer, $data);
        $this->clear();

        return $retailer;
    }

    public function delete(Retailer $retailer): void
    {
        $this->repository->delete($retailer);
        $this->clear();
    }
}
