<?php

declare(strict_types=1);

namespace App\Http\Resources;

use BackedEnum;
use DateTimeInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource as BaseJsonResource;
use Illuminate\Support\Carbon;

class JsonResource extends BaseJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * Concrete resources should read data through model getter methods instead
     * of reaching into raw attributes directly.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return parent::toArray($request);
    }

    protected function publicId(): ?string
    {
        $resource = $this->resource;

        if (is_object($resource) && method_exists($resource, 'getPublicId')) {
            return $resource->getPublicId();
        }

        return null;
    }

    protected function dateTime(null|DateTimeInterface|string $value): ?string
    {
        if ($value instanceof DateTimeInterface) {
            return Carbon::instance($value)->utc()->toIso8601String();
        }

        if (is_string($value) && $value !== '') {
            return Carbon::parse($value)->utc()->toIso8601String();
        }

        return null;
    }

    /**
     * @return array{amount_minor: int, currency_code: string}|null
     */
    protected function money(?int $amountMinor, ?string $currencyCode): ?array
    {
        if ($amountMinor === null && $currencyCode === null) {
            return null;
        }

        return [
            'amount_minor'  => (int) $amountMinor,
            'currency_code' => (string) $currencyCode,
        ];
    }

    protected function enum(BackedEnum|string|null $value): ?string
    {
        if ($value instanceof BackedEnum) {
            return (string) $value->value;
        }

        return $value;
    }
}
