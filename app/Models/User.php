<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\Archivable;
use App\Models\Concerns\HasPublicId;
use App\Support\Auth\Passport\Contracts\UserContract;
use App\Support\Collection;
use App\Support\Resources\Contracts\TransformableContract;
use App\Support\Resources\Contracts\UuidAsPrimaryContract;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Passport\HasApiTokens;
use Laravel\Passport\Token;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements TransformableContract, UserContract, UuidAsPrimaryContract
{
    use Archivable;
    use HasApiTokens;
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
        'company_id',
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

    protected static function booted(): void
    {
        static::updated(static function (self $user): void {
            if ($user->wasChanged(['password', 'archived_at', 'email_verified_at'])) {
                $user->revokeAccessTokens();
            }
        });
    }

    public function revokeAccessTokens(): void
    {
        $this->tokens()
            ->where('revoked', false)
            ->each(static function (Token $token): void {
                $token->revoke();
                $token->refreshToken?->revoke();
            });
    }

    public function getRouteKeyName(): string
    {
        return 'public_id';
    }

    protected function getDefaultGuardName(): string
    {
        return 'web';
    }

    public function getId(): int
    {
        return (int) $this->getKey();
    }

    public function getPublicId(): string
    {
        return (string) $this->getAttribute('public_id');
    }

    public function getIdentifier(): string
    {
        return (string) $this->getKey();
    }

    public function isEligibleForAuthentication(): bool
    {
        return $this->getAttribute('archived_at') === null
            && $this->getAttribute('email_verified_at') !== null;
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

    public function getName(): string
    {
        return (string) $this->getAttribute('name');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
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
