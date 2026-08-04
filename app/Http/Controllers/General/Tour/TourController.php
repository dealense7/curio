<?php

declare(strict_types=1);

namespace App\Http\Controllers\General\Tour;

use App\Contracts\Services\Tour\TourServiceContract;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Tour\TourFilterRequest;
use App\Http\Resources\Tour\TourConfigResource;
use App\Http\Resources\Tour\TourResource;
use Illuminate\Http\JsonResponse;

class TourController extends ApiController
{
    private const array RELATIONS = ['difficulty', 'currency', 'publishingStatus', 'routeFile', 'coverImage', 'bestMonths'];

    public function __construct(private readonly TourServiceContract $tourService) {}

    public function config(): JsonResponse
    {
        return (new TourConfigResource($this->tourService->getConfig()))
            ->additional(['message' => __('api.ok')])
            ->response();
    }

    public function index(TourFilterRequest $request): JsonResponse
    {
        $tours = $this->tourService->getPublishedItems($request->filters(), self::RELATIONS);

        return $this->resource($tours, TourResource::class, self::RELATIONS);
    }

    public function show(string $tourPublicId): JsonResponse
    {
        $tour = $this->tourService->findPublishedByPublicId($tourPublicId, self::RELATIONS);

        return $tour === null
            ? $this->resourceNotFound()
            : $this->resource($tour, TourResource::class, self::RELATIONS);
    }
}
