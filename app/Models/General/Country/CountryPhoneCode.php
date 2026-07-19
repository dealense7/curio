<?php

declare(strict_types=1);

namespace App\Models\General\Country;

use App\Models\Model;
use Database\Factories\CountryPhoneCodeFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use InvalidArgumentException;

#[UseFactory(CountryPhoneCodeFactory::class)]
class CountryPhoneCode extends Model
{
    protected $table = 'country_phone_codes';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'country_id',
        'phone_code',
        'is_primary',
        'sort_order',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'country_id'  => 'integer',
            'is_primary'  => 'boolean',
            'sort_order'  => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saving(static function (self $countryPhoneCode): void {
            if (! preg_match('/^\+[0-9]+$/', $countryPhoneCode->phone_code)) {
                throw new InvalidArgumentException('The phone code format is invalid.');
            }
        });
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function getCountryId(): int
    {
        return (int) $this->getAttribute('country_id');
    }

    public function getPhoneCode(): string
    {
        return (string) $this->getAttribute('phone_code');
    }

    public function getIsPrimary(): bool
    {
        return (bool) $this->getAttribute('is_primary');
    }

    public function getSortOrder(): int
    {
        return (int) $this->getAttribute('sort_order');
    }
}
