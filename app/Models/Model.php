<?php

declare(strict_types=1);

namespace App\Models;

use App\Support\Collection;
use App\Support\Resources\Contracts\TransformableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Support\Carbon;

abstract class Model extends EloquentModel implements TransformableContract
{
    use HasFactory;

    public const string PERMISSIONS_SCOPE = 'models';

    /**
     * Internal keys and audit columns must never be mass assignable by default.
     *
     * @var list<string>
     */
    protected $guarded = [
        'id',
        'public_id',
        'version',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'archived_at',
    ];

    public function getId(): int
    {
        return (int) $this->getKey();
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
