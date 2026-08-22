<?php

declare(strict_types=1);

namespace App\Services\Retailer;

use App\Contracts\Repositories\Retailer\RetailerRepositoryContract;
use App\Contracts\Services\Retailer\RetailerServiceContract;
use App\Models\Retailer\Retailer;
use App\Services\Service;
use App\Support\Collection;
use App\Support\Helpers\Helper;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class RetailerService extends Service implements RetailerServiceContract
{
    public function __construct(private readonly RetailerRepositoryContract $repository) {}

    public function getItems(array $filters = [], array $relations = [], ?string $sort = null): Collection
    {
        $this->authorize('read', new Retailer);

        return $this->repository->getItems($filters, $relations, $sort);
    }

    public function getItemsWithPagination(
        array $filters = [],
        array $relations = [],
        int $page = 1,
        ?int $perPage = null,
        ?string $sort = null,
    ): LengthAwarePaginator {
        $this->authorize('read', new Retailer);

        return $this->repository->getItemsWithPagination($filters, $relations, $page, $perPage, $sort);
    }

    public function findByPublicId(string $publicId, array $relations = []): ?Retailer
    {
        $retailer = $this->repository->findByPublicId($publicId, $relations);

        if ($retailer === null) {
            return null;
        }

        $this->authorize('read', $retailer);

        return $retailer;
    }

    public function slugExists(string $slug, ?string $exceptPublicId = null): bool
    {
        return $this->repository->slugExists($slug, $exceptPublicId);
    }

    public function create(array $data): Retailer
    {
        $this->authorize('create', new Retailer);
        $actor = Helper::getUser();

        if ($actor !== null) {
            $data['created_by'] = $actor->getId();
            $data['updated_by'] = $actor->getId();
        }

        return DB::transaction(fn (): Retailer => $this->repository->create($data));
    }

    public function update(Retailer $retailer, array $data): Retailer
    {
        $this->authorize('update', $retailer);
        $actor = Helper::getUser();

        if ($actor !== null) {
            $data['updated_by'] = $actor->getId();
        }

        return DB::transaction(fn (): Retailer => $this->repository->fillData($retailer, $data));
    }

    public function delete(Retailer $retailer): void
    {
        $this->authorize('delete', $retailer);
        DB::transaction(function () use ($retailer): void {
            $this->repository->delete($retailer);
        });
    }
}
