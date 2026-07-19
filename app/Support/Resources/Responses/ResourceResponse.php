<?php

declare(strict_types=1);

namespace App\Support\Resources\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceResponse as BaseResourceResponse;

use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;

use function response;
use function tap;

class ResourceResponse extends BaseResourceResponse
{
    public function toResponse($request): JsonResponse
    {
        $jsonOptions = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;

        return tap(response()->json(
            $this->wrap(
                $this->resource->resolve($request),
                $this->resource->with($request),
                $this->resource->additional,
            ),
            $this->calculateStatus(),
            [],
            $jsonOptions,
        ), function ($response) use ($request) {
            $response->original = $this->resource->resource;

            $this->resource->withResponse($request, $response);
        });
    }
}
