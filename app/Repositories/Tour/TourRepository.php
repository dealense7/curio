<?php

declare(strict_types=1);

namespace App\Repositories\Tour;

use App\Contracts\Repositories\Tour\TourRepositoryContract;
use App\Enums\General\PublishingStatus as PublishingStatusEnum;
use App\Models\General\Currency;
use App\Models\General\Difficulty;
use App\Models\General\Month;
use App\Models\General\PublishingStatus;
use App\Models\Tour\Tour;
use App\Repositories\Repository;
use App\Support\Collection;
use Illuminate\Database\Eloquent\Builder;

class TourRepository extends Repository implements TourRepositoryContract
{
    /** @return array<string, Collection> */
    public function getConfig(): array
    {
        return [
            'difficulties'            => Difficulty::query()->orderBy('id')->get(),
            'publishing_statuses'     => PublishingStatus::query()->orderBy('id')->get(),
            'currencies'              => Currency::query()->orderBy('id')->get(),
            'months'                  => Month::query()->orderBy('sort_order')->get(),
        ];
    }

    /** @return list<string> */
    private function getTourColumns(): array
    {
        return [
            'id',
            'public_id',
            'title',
            'description',
            'start_location',
            'end_location',
            'distance_km',
            'duration_comfortable_days',
            'duration_recommended_days',
            'daily_distance_comfortable_km',
            'daily_distance_recommended_km',
            'elevation_gain_m',
            'difficulty_id',
            'price_comfortable',
            'price_recommended',
            'average_daily_spend',
            'currency_id',
            'route_file_id',
            'cover_image_id',
            'publishing_status_id',
            'created_at',
            'updated_at',
        ];
    }

    /** @return list<string> */
    private function getFileColumns(): array
    {
        return [
            'id',
            'public_id',
            'original_name',
            'name',
            'folder',
            'extension',
            'mime',
            'size',
            'disk',
            'type',
            'status',
            'created_at',
            'updated_at',
        ];
    }

    /** @return list<string> */
    private function getLookupColumns(): array
    {
        return ['id', 'key', 'display_name', 'created_at', 'updated_at'];
    }

    /**
     * @param  list<string>  $relations
     * @return array<int|string, mixed>
     */
    private function mapRelations(array $relations): array
    {
        return collect($relations)
            ->mapWithKeys(function (string $relation): array {
                return match ($relation) {
                    'routeFile', 'coverImage' => [
                        $relation => fn ($query) => $query->select($this->getFileColumns()),
                    ],
                    'difficulty', 'currency', 'publishingStatus' => [
                        $relation => fn ($query) => $query->select($this->getLookupColumns()),
                    ],
                    'bestMonths' => [
                        $relation => fn ($query) => $query
                            ->select([...$this->getLookupColumns(), 'sort_order'])
                            ->orderBy('sort_order'),
                    ],
                    default => [$relation => $relation],
                };
            })
            ->all();
    }

    public function getItems(array $filters = [], array $relations = []): Collection
    {
        /** @var Collection<int, Tour> $items */
        $items = $this->applyFilters($this->getData(), $filters)
            ->select($this->getTourColumns())
            ->with($this->mapRelations($relations))
            ->orderByDesc('created_at')
            ->get();

        return $items;
    }

    public function getPublishedItems(array $filters = [], array $relations = []): Collection
    {
        return $this->getItems([
            ...$filters,
            'publishing_status_key' => PublishingStatusEnum::PUBLISHED->value,
        ], $relations);
    }

    public function findByPublicId(string $publicId, array $relations = []): ?Tour
    {
        /** @var ?Tour $tour */
        $tour = $this->getData()
            ->select($this->getTourColumns())
            ->with($this->mapRelations($relations))
            ->where('public_id', $publicId)
            ->first();

        return $tour;
    }

    public function findPublishedByPublicId(string $publicId, array $relations = []): ?Tour
    {
        /** @var ?Tour $tour */
        $tour = $this->getData()
            ->select($this->getTourColumns())
            ->with($this->mapRelations($relations))
            ->where('public_id', $publicId)
            ->whereHas('publishingStatus', fn (Builder $query): Builder => $query->where('key', PublishingStatusEnum::PUBLISHED->value))
            ->first();

        return $tour;
    }

    public function create(array $data): Tour
    {
        return $this->fillData($this->getModel(), $data);
    }

    public function fillData(Tour $tour, array $data): Tour
    {
        $tour->fill($data);
        $tour->saveOrFail();

        return $tour;
    }

    public function syncBestMonths(Tour $tour, array $monthIds): Tour
    {
        $tour->bestMonths()->sync($monthIds);

        return $tour->fresh(['difficulty', 'currency', 'publishingStatus', 'routeFile', 'coverImage', 'bestMonths']);
    }

    public function delete(Tour $tour): void
    {
        $tour->delete();
    }

    public function getModel(): Tour
    {
        return new Tour;
    }

    private function applyFilters(Builder $query, array $filters): Builder
    {
        return $query
            ->when(
                isset($filters['publishing_status_id']),
                fn (Builder $query): Builder => $query->where('publishing_status_id', $filters['publishing_status_id']),
            )
            ->when(
                isset($filters['publishing_status_key']),
                fn (Builder $query): Builder => $query->whereHas(
                    'publishingStatus',
                    fn (Builder $query): Builder => $query->where('key', $filters['publishing_status_key']),
                ),
            )
            ->when(
                isset($filters['difficulty_id']),
                fn (Builder $query): Builder => $query->where('difficulty_id', $filters['difficulty_id']),
            )
            ->when(
                isset($filters['month_id']),
                fn (Builder $query): Builder => $query->whereHas(
                    'bestMonths',
                    fn (Builder $query): Builder => $query->where('months.id', $filters['month_id']),
                ),
            );
    }
}
