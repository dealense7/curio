<?php

declare(strict_types=1);

namespace App\CacheRepositories\Tour;

use App\CacheRepositories\CacheRepository;
use App\Contracts\Repositories\Tour\TourRepositoryContract;
use App\Models\Tour\Tour;
use App\Repositories\Tour\TourRepository;
use App\Support\Collection;

class TourCacheRepository extends CacheRepository implements TourRepositoryContract
{
    protected string $cacheKey = Tour::class;

    public function __construct(private readonly TourRepository $repository) {}

    public function getConfig(): array
    {
        return $this->remember(
            $this->createKey('config'),
            fn (): array => $this->repository->getConfig(),
        );
    }

    public function getItems(array $filters = [], array $relations = []): Collection
    {
        return $this->remember(
            $this->createKey('items', [$filters, $relations]),
            fn (): Collection => $this->repository->getItems($filters, $relations),
        );
    }

    public function getPublishedItems(array $filters = [], array $relations = []): Collection
    {
        return $this->remember(
            $this->createKey('published_items', [$filters, $relations]),
            fn (): Collection => $this->repository->getPublishedItems($filters, $relations),
        );
    }

    public function findByPublicId(string $publicId, array $relations = []): ?Tour
    {
        return $this->rememberNullable(
            $this->createKey('find_by_public_id', [$publicId, $relations]),
            fn (): ?Tour => $this->repository->findByPublicId($publicId, $relations),
        );
    }

    public function findPublishedByPublicId(string $publicId, array $relations = []): ?Tour
    {
        return $this->rememberNullable(
            $this->createKey('find_published_by_public_id', [$publicId, $relations]),
            fn (): ?Tour => $this->repository->findPublishedByPublicId($publicId, $relations),
        );
    }

    public function create(array $data): Tour
    {
        $tour = $this->repository->create($data);
        $this->clear();

        return $tour;
    }

    public function fillData(Tour $tour, array $data): Tour
    {
        $tour = $this->repository->fillData($tour, $data);
        $this->clear();

        return $tour;
    }

    public function syncBestMonths(Tour $tour, array $monthIds): Tour
    {
        $tour = $this->repository->syncBestMonths($tour, $monthIds);
        $this->clear();

        return $tour;
    }

    public function delete(Tour $tour): void
    {
        $this->repository->delete($tour);
        $this->clear();
    }

    public function clearCache(): void
    {
        $this->clear();
    }
}
