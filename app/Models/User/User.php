<?php

declare(strict_types=1);

namespace App\Models\User;

use App\Contracts\Models\User\UserContract;
use App\Enums\User\UserStatus;
use App\Support\Collection;
use App\Support\Resources\Contracts\TransformableContract;
use App\Support\Resources\Contracts\UuidAsPrimaryContract;
use App\Support\Traits\Archivable;
use App\Support\Traits\HasPublicId;
use Database\Factories\User\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Laravel\Passport\HasApiTokens;
use Laravel\Passport\Token;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['first_name', 'last_name', 'email', 'status', 'preferred_locale', 'password'])]
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
            'last_login_at'     => 'datetime',
            'password'          => 'hashed',
            'status'            => UserStatus::class,
        ];
    }

    public function setEmailAttribute(?string $email): void
    {
        $this->attributes['email'] = $email === null ? null : Str::lower(trim($email));
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->getFirstName().' '.$this->getLastName());
    }

    public static function normalizeEmail(string $email): string
    {
        return Str::lower(trim($email));
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
            && $this->getAttribute('status')            !== UserStatus::SUSPENDED
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

    public function getFirstName(): string
    {
        return (string) $this->getAttribute('first_name');
    }

    public function getLastName(): string
    {
        return (string) $this->getAttribute('last_name');
    }

    public function getFullName(): string
    {
        return $this->getFullNameAttribute();
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(UserContact::class)->whereNull('archived_at');
    }

    public function getEmail(): string
    {
        return (string) $this->getAttribute('email');
    }

    public function getStatus(): string
    {
        return $this->getAttribute('status')->value;
    }

    public function getPreferredLocale(): string
    {
        return (string) $this->getAttribute('preferred_locale');
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
