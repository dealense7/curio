<?php

declare(strict_types=1);

namespace App\Contracts\Repositories\Tour;

use App\Models\Tour\Tour;
use App\Support\Collection;

interface TourRepositoryContract
{
    /** @return array<string, Collection> */
    public function getConfig(): array;

    public function getItems(array $filters = [], array $relations = []): Collection;

    public function getPublishedItems(array $filters = [], array $relations = []): Collection;

    public function findByPublicId(string $publicId, array $relations = []): ?Tour;

    public function findPublishedByPublicId(string $publicId, array $relations = []): ?Tour;

    public function create(array $data): Tour;

    public function fillData(Tour $tour, array $data): Tour;

    /** @param list<int> $monthIds */
    public function syncBestMonths(Tour $tour, array $monthIds): Tour;

    public function delete(Tour $tour): void;
}
