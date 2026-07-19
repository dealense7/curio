<?php

declare(strict_types=1);

namespace App\Support\Resources;

use App\Support\Resources\Responses\PaginatedResourceResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection as BaseResourceCollection;

use function array_merge_recursive;
use function is_null;

class JsonResourceCollection extends BaseResourceCollection
{
    public function toArray($request): array
    {
        /** @var \App\Support\Resources\JsonResource $item */
        foreach ($this->collection as $item) {
            $item->setDataWrapper('');
        }

        return ['data' => $this->collection];
    }

    public function withRelations(array $relations = []): self
    {
        /** @var \App\Support\Resources\JsonResource $item */
        foreach ($this->collection as $item) {
            $item->withRelations($relations);
        }

        return $this;
    }

    public function appendAdditional(array $data): self
    {
        $this->additional = array_merge_recursive($this->additional, $data);

        return $this;
    }

    protected function preparePaginatedResponse($request): JsonResponse
    {
        if ($this->preserveAllQueryParameters) {
            $this->resource->appends($request->query());
        } elseif (! is_null($this->queryParameters)) {
            $this->resource->appends($this->queryParameters);
        }

        return (new PaginatedResourceResponse($this))->toResponse($request);
    }
}
