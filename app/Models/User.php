<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\Archivable;
use App\Models\Concerns\HasPublicId;
use App\Support\Collection;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    use Archivable;
    /** @use HasFactory<UserFactory> */
    use HasFactory;
    use HasPublicId;
    use HasRoles;
    use Notifiable;

    /**
     * Internal identifiers should never be mass assignable.
     *
     * @var list<string>
     */
    protected $guarded = [
        'id',
        'public_id',
        'created_at',
        'updated_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

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

    public function getName(): string
    {
        return (string) $this->getAttribute('name');
    }

    public function getEmail(): string
    {
        return (string) $this->getAttribute('email');
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
}
