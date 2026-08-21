<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CompanyStatus;
use App\Models\Concerns\HasPublicId;
use App\Models\General\Country\Country;
use App\Models\General\Currency;
use Database\Factories\CompanyFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use LogicException;

#[UseFactory(CompanyFactory::class)]
class Company extends Model
{
    use HasPublicId;

    public const string PERMISSIONS_SCOPE = 'companies';

    protected $table = 'companies';

    /** @var list<string> */
    protected $fillable = [
        'display_name',
        'legal_name',
        'slug',
        'status',
        'country_id',
        'default_currency_id',
        'timezone',
        'default_locale',
        'support_email',
        'support_phone',
        'website_url',
        'logo_path',
        'suspended_at',
        'suspension_reason',
        'archived_at',
    ];

    protected function casts(): array
    {
        return [
            'status'              => CompanyStatus::class,
            'country_id'          => 'integer',
            'default_currency_id' => 'integer',
            'suspended_at'        => 'datetime',
            'archived_at'         => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(static function (self $company): void {
            $company->slug = self::uniqueSlug($company->slug ?: $company->display_name);
        });

        static::deleting(static function (self $company): void {
            if ($company->users()->exists()) {
                throw new LogicException('A company with operational users cannot be hard deleted.');
            }
        });
    }

    public static function uniqueSlug(string $value, ?int $ignoreId = null): string
    {
        $base   = Str::slug($value);
        $base   = Str::limit($base !== '' ? $base : 'company', 80, '');
        $slug   = $base;
        $suffix = 2;

        while (static::query()->when($ignoreId !== null, fn ($query) => $query->where('id', '!=', $ignoreId))->where('slug', $slug)->exists()) {
            $suffixText = '-'.$suffix++;
            $slug       = Str::limit($base, 80 - strlen($suffixText), '').$suffixText;
        }

        return $slug;
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function defaultCurrency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'default_currency_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function getDisplayName(): string
    {
        return (string) $this->getAttribute('display_name');
    }

    public function getLegalName(): ?string
    {
        return $this->getAttribute('legal_name');
    }

    public function getSlug(): string
    {
        return (string) $this->getAttribute('slug');
    }

    public function getStatus(): string
    {
        return $this->getAttribute('status')->value;
    }

    public function getCountryId(): int
    {
        return (int) $this->getAttribute('country_id');
    }

    public function getDefaultCurrencyId(): int
    {
        return (int) $this->getAttribute('default_currency_id');
    }

    public function getTimezone(): string
    {
        return (string) $this->getAttribute('timezone');
    }

    public function getDefaultLocale(): string
    {
        return (string) $this->getAttribute('default_locale');
    }

    public function getSupportEmail(): ?string
    {
        return $this->getAttribute('support_email');
    }

    public function getSupportPhone(): ?string
    {
        return $this->getAttribute('support_phone');
    }

    public function getWebsiteUrl(): ?string
    {
        return $this->getAttribute('website_url');
    }

    public function getLogoPath(): ?string
    {
        return $this->getAttribute('logo_path');
    }

    public function getSuspendedAt(): ?Carbon
    {
        return $this->getAttribute('suspended_at');
    }

    public function getSuspensionReason(): ?string
    {
        return $this->getAttribute('suspension_reason');
    }

    public function getArchivedAt(): ?Carbon
    {
        return $this->getAttribute('archived_at');
    }
}
