<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Support\Resources\Responses\ApiResponse;
use App\Support\Resources\Responses\ArrayResource;
use App\Support\Resources\Responses\ErrorResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ApiController extends Controller
{
    protected function error(string $error, int $code = 500, array $headers = [], array $additional = []): JsonResponse
    {
        return ApiResponse::resource($error, $additional, ErrorResource::class)
            ->response()
            ->setStatusCode($code)
            ->withHeaders($headers);
    }

    protected function resourceNotFound(?string $message = null): JsonResponse
    {
        return $this->error($message ?? __('api.item_not_found'), ResponseAlias::HTTP_NOT_FOUND);
    }

    protected function throwCustomValidationError(string $field, string $error): never
    {
        throw ValidationException::withMessages([$field => $error]);
    }

    protected function getInputFilters(Request $request): array
    {
        return (array) $request->input('filters');
    }

    protected function getInputPage(Request $request): int
    {
        return (int) $request->input('page', 1);
    }

    protected function getInputPerPage(Request $request): ?int
    {
        $perPage = $request->input('perPage');

        return $perPage === null ? null : (int) $perPage;
    }

    protected function getInputSort(Request $request): ?string
    {
        $sort = $request->input('sort');

        return is_string($sort) ? $sort : null;
    }

    protected function success(string $message, array $data = [], int $code = 200, array $headers = []): JsonResponse
    {
        return ApiResponse::resource($message, $data, ArrayResource::class)
            ->response()
            ->setStatusCode($code)
            ->withHeaders($headers);
    }

    protected function resource(mixed $data, string $resourceType, array $includes = []): JsonResponse
    {
        return ApiResponse::resource(__('api.ok'), $data, $resourceType, $includes)->response();
    }
}
