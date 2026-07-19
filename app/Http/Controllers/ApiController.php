<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Support\Resources\Responses\ApiResponse;
use App\Support\Resources\Responses\ArrayResource;
use App\Support\Resources\Responses\ErrorResource;
use Illuminate\Http\JsonResponse;
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
