<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasPublicId;
use App\Support\Collection;
use App\Support\Resources\Contracts\TransformableContract;
use App\Support\Resources\Contracts\UuidAsPrimaryContract;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

abstract class Model extends EloquentModel implements TransformableContract, UuidAsPrimaryContract
{
    use HasPublicId;
    use SoftDeletes;

    /**
     * Internal keys and audit columns must never be mass assignable by default.
     *
     * @var list<string>
     */
    protected $guarded = [
        'id',
        'public_id',
        'company_id',
        'version',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'archived_at',
    ];

    public function getRouteKeyName(): string
    {
        return 'public_id';
    }

    public function getId(): int
    {
        return (int) $this->getKey();
    }

    public function getPublicId(): string
    {
        return (string) $this->getAttribute('public_id');
    }

    public function getUuid(): string
    {
        return $this->getPublicId();
    }

    public function getUuidString(): string
    {
        return $this->getPublicId();
    }

    public function getModel(): static
    {
        return $this;
    }

    public function getCreatedAt(): ?Carbon
    {
        /** @var ?Carbon $createdAt */
        $createdAt = $this->getAttribute('created_at');

        return $createdAt;
    }

    public function getUpdatedAt(): ?Carbon
    {
        /** @var ?Carbon $updatedAt */
        $updatedAt = $this->getAttribute('updated_at');

        return $updatedAt;
    }

    /**
     * @param  array<int, static>  $models
     * @return Collection<int, static>
     */
    public function newCollection(array $models = []): Collection
    {
        return new Collection($models);
    }

    public static function getPermission(string $permission): string
    {
        return static::PERMISSIONS_SCOPE.'.'.$permission;
    }
}
