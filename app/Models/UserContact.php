<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\UserContactType;
use App\Models\Concerns\Archivable;
use App\Models\Concerns\HasPublicId;
use Database\Factories\UserContactFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class UserContact extends Model
{
    use Archivable;
    /** @use HasFactory<UserContactFactory> */
    use HasFactory;
    use HasPublicId;

    protected $fillable = [
        'type',
        'label',
        'value',
        'is_primary',
    ];

    protected function casts(): array
    {
        return [
            'type'       => UserContactType::class,
            'is_primary' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(static function (self $contact): void {
            if ($contact->company_id === null && $contact->user_id !== null) {
                $contact->company_id = User::query()
                    ->whereKey($contact->user_id)
                    ->value('company_id');
            }
        });

        static::saving(static function (self $contact): void {
            $contact->value = match ($contact->type) {
                UserContactType::EMAIL                           => Str::lower(trim((string) $contact->value)),
                UserContactType::PHONE, UserContactType::ADDRESS => trim((string) $contact->value),
            };

        });

        static::saved(static function (self $contact): void {
            if (! $contact->is_primary || $contact->archived_at !== null) {
                return;
            }

            self::query()
                ->where('user_id', $contact->user_id)
                ->where('type', $contact->type->value)
                ->whereKey('!=', $contact->getKey())
                ->where('is_primary', true)
                ->update(['is_primary' => false]);
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getType(): string
    {
        return $this->type->value;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function getValue(): string
    {
        return (string) $this->value;
    }

    public function getIsPrimary(): bool
    {
        return (bool) $this->is_primary;
    }

    public function isPrimary(): bool
    {
        return $this->getIsPrimary();
    }
}
