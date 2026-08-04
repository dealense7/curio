<?php

declare(strict_types=1);

namespace App\Support\Resources\Responses;

use App\Support\Resources\JsonResource;

use function array_merge;

class ErrorResource extends JsonResource
{
    public static $wrap = null;

    public function __construct(array|string $resource)
    {
        $this->resource = $resource;
        $this->setDataWrapper('');
    }

    public function toArray($request): array
    {
        $result = array_merge(
            [
                'errors' => [
                    'general' => is_array($this->resource) ? $this->resource : [$this->resource],
                ],
            ],
            $this->additional,
        );

        $this->additional([]);

        return $result;
    }
}
