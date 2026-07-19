<?php

declare(strict_types=1);

namespace App\Support\Resources\Responses;

use App\Support\Resources\JsonResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

abstract class ApiResponse
{
    public static function resource(
        string $message,
        mixed $data,
        string $resourceType,
        array $includes = [],
    ): JsonResource {
        if ($data instanceof LengthAwarePaginator || $data instanceof Collection) {
            $resource = new JsonResourceCollection($data, $resourceType);
        } else {
            /** @var \Illuminate\Http\Resources\Json\JsonResource $resource */
            $resource = new $resourceType($data);
        }

        if (! empty($includes)) {
            /** @phpstan-ignore-next-line */
            $resource->withRelations($includes);
        }

        $resource->additional(['message' => $message]);

        return $resource;
    }
}
