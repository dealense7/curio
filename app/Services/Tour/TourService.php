<?php

declare(strict_types=1);

namespace App\Services\Tour;

use App\CacheRepositories\Tour\TourCacheRepository;
use App\Contracts\Repositories\Tour\TourRepositoryContract;
use App\Contracts\Services\Tour\TourServiceContract;
use App\Models\Tour\Tour;
use App\Repositories\Tour\TourRepository;
use App\Services\Service;
use App\Support\Collection;
use Illuminate\Support\Facades\DB;

class TourService extends Service implements TourServiceContract
{
    public function __construct(
        private readonly TourRepositoryContract $cachedRepository,
        private readonly TourRepository $repository,
        private readonly TourCacheRepository $cacheRepository,
    ) {}

    public function getConfig(): array
    {
        return $this->cachedRepository->getConfig();
    }

    public function getItems(array $filters = [], array $relations = []): Collection
    {
        $this->authorize('read', new Tour);

        return $this->cachedRepository->getItems($filters, $relations);
    }

    public function getPublishedItems(array $filters = [], array $relations = []): Collection
    {
        return $this->cachedRepository->getPublishedItems($filters, $relations);
    }

    public function findByPublicId(string $publicId, array $relations = []): ?Tour
    {
        $tour = $this->cachedRepository->findByPublicId($publicId, $relations);

        if ($tour !== null) {
            $this->authorize('read', $tour);
        }

        return $tour;
    }

    public function findPublishedByPublicId(string $publicId, array $relations = []): ?Tour
    {
        return $this->cachedRepository->findPublishedByPublicId($publicId, $relations);
    }

    public function create(array $tourData, array $bestMonthIds): Tour
    {
        $this->authorize('create', new Tour);

        /** @var Tour $tour */
        $tour = DB::transaction(function () use ($tourData, $bestMonthIds): Tour {
            $tour = $this->repository->create($tourData);

            return $this->repository->syncBestMonths($tour, $bestMonthIds);
        });
        $this->cacheRepository->clearCache();

        return $tour;
    }

    public function update(Tour $tour, array $tourData, array $bestMonthIds): Tour
    {
        $this->authorize('update', $tour);

        /** @var Tour $tour */
        $tour = DB::transaction(function () use ($tour, $tourData, $bestMonthIds): Tour {
            $tour = $this->repository->fillData($tour, $tourData);

            return $this->repository->syncBestMonths($tour, $bestMonthIds);
        });
        $this->cacheRepository->clearCache();

        return $tour;
    }

    public function delete(Tour $tour): void
    {
        $this->authorize('delete', $tour);
        $this->repository->delete($tour);
        $this->cacheRepository->clearCache();
    }
}
