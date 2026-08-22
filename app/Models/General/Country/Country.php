<?php

declare(strict_types=1);

namespace App\Models\General\Country;

use App\Support\Traits\HasPublicId;
use App\Models\Model;
use App\Support\Resources\Contracts\UuidAsPrimaryContract;
use Database\Factories\CountryFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

#[UseFactory(CountryFactory::class)]
class Country extends Model implements UuidAsPrimaryContract
{
    use HasPublicId;
    use SoftDeletes;

    public const string PERMISSIONS_SCOPE = 'countries';

    protected $table = 'countries';

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'is_active'  => true,
        'sort_order' => 0,
    ];

    /**
     * @var list<string>
     */
    protected $fillable = [
        'iso2',
        'iso3',
        'numeric_code',
        'name',
        'official_name',
        'is_active',
        'sort_order',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active'  => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saving(static function (self $country): void {
            $country->iso2 = strtoupper($country->iso2);
            $country->iso3 = strtoupper($country->iso3);
        });

        static::deleting(static function (self $country): void {
            $country->phoneCodes()->delete();
        });
    }

    public function phoneCodes(): HasMany
    {
        return $this->hasMany(CountryPhoneCode::class, 'country_id');
    }

    public function primaryPhoneCode(): HasOne
    {
        return $this->hasOne(CountryPhoneCode::class, 'country_id')->where('is_primary', true);
    }

    public function getIso2(): string
    {
        return (string) $this->getAttribute('iso2');
    }

    public function getIso3(): string
    {
        return (string) $this->getAttribute('iso3');
    }

    public function getNumericCode(): ?string
    {
        /** @var ?string $numericCode */
        $numericCode = $this->getAttribute('numeric_code');

        return $numericCode;
    }

    public function getName(): string
    {
        return (string) $this->getAttribute('name');
    }

    public function getOfficialName(): ?string
    {
        /** @var ?string $officialName */
        $officialName = $this->getAttribute('official_name');

        return $officialName;
    }

    public function getIsActive(): bool
    {
        return (bool) $this->getAttribute('is_active');
    }

    public function getSortOrder(): int
    {
        return (int) $this->getAttribute('sort_order');
    }
}
