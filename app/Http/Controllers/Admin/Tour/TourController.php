<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Tour;

use App\Contracts\Services\Tour\TourServiceContract;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Admin\Tour\StoreTourRequest;
use App\Http\Requests\Admin\Tour\UpdateTourRequest;
use App\Http\Requests\Tour\TourFilterRequest;
use App\Http\Resources\Tour\TourResource;
use Illuminate\Http\JsonResponse;

class TourController extends ApiController
{
    private const array RELATIONS = ['difficulty', 'currency', 'publishingStatus', 'routeFile', 'coverImage', 'bestMonths'];

    public function __construct(private readonly TourServiceContract $tourService) {}

    public function index(TourFilterRequest $request): JsonResponse
    {
        $tours = $this->tourService->getItems($request->filters(includePublishingStatus: true), self::RELATIONS);

        return $this->resource($tours, TourResource::class, self::RELATIONS);
    }

    public function store(StoreTourRequest $request): JsonResponse
    {
        $tour = $this->tourService->create($request->validatedTour(), $request->validatedBestMonthIds());

        return $this->resource($tour, TourResource::class, self::RELATIONS);
    }

    public function show(string $tourPublicId): JsonResponse
    {
        $tour = $this->tourService->findByPublicId($tourPublicId, self::RELATIONS);

        return $tour === null
            ? $this->resourceNotFound()
            : $this->resource($tour, TourResource::class, self::RELATIONS);
    }

    public function update(UpdateTourRequest $request, string $tourPublicId): JsonResponse
    {
        $tour = $this->tourService->findByPublicId($tourPublicId, self::RELATIONS);
        if ($tour === null) {
            return $this->resourceNotFound();
        }

        $tour = $this->tourService->update($tour, $request->validatedTour(), $request->validatedBestMonthIds());

        return $this->resource($tour, TourResource::class, self::RELATIONS);
    }

    public function destroy(string $tourPublicId): JsonResponse
    {
        $tour = $this->tourService->findByPublicId($tourPublicId);
        if ($tour === null) {
            return $this->resourceNotFound();
        }

        $this->tourService->delete($tour);

        return $this->success(__('tour.deleted_successfully'));
    }
}
