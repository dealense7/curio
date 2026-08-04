<?php

declare(strict_types=1);

namespace App\Contracts\Services\Tour;

use App\Models\Tour\Tour;
use App\Support\Collection;

interface TourServiceContract
{
    /** @return array<string, Collection> */
    public function getConfig(): array;

    public function getItems(array $filters = [], array $relations = []): Collection;

    public function getPublishedItems(array $filters = [], array $relations = []): Collection;

    public function findByPublicId(string $publicId, array $relations = []): ?Tour;

    public function findPublishedByPublicId(string $publicId, array $relations = []): ?Tour;

    /** @param list<int> $bestMonthIds */
    public function create(array $tourData, array $bestMonthIds): Tour;

    /** @param list<int> $bestMonthIds */
    public function update(Tour $tour, array $tourData, array $bestMonthIds): Tour;

    public function delete(Tour $tour): void;
}
