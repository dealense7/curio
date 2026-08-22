<?php

declare(strict_types=1);

namespace App\Models\Retailer;

use App\Models\Concerns\HasPublicId;
use App\Models\General\Country\Country;
use App\Models\General\Currency;
use App\Models\Model;
use App\Models\User;
use App\Support\Resources\Contracts\UuidAsPrimaryContract;
use Database\Factories\Retailer\RetailerFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

#[UseFactory(RetailerFactory::class)]
class Retailer extends Model implements UuidAsPrimaryContract
{
    use HasPublicId;
    use SoftDeletes;

    public const string PERMISSIONS_SCOPE = 'retailers';

    protected array $sortFields = [
        'name',
        'slug',
        'domain',
        'created_at',
        'updated_at',
        'is_active',
        'scraping_enabled',
    ];

    /** @var array<string, string> */
    protected array $sortBy = ['name' => 'asc'];

    protected $table = 'retailers';

    protected $fillable = [
        'name',
        'slug',
        'domain',
        'country_id',
        'currency_id',
        'is_active',
        'scraping_enabled',
        'last_scraped_at',
    ];

    protected function casts(): array
    {
        return [
            'country_id'       => 'integer',
            'currency_id'      => 'integer',
            'is_active'        => 'boolean',
            'scraping_enabled' => 'boolean',
            'last_scraped_at'  => 'datetime',
        ];
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getName(): string
    {
        return (string) $this->getAttribute('name');
    }

    public function getSlug(): string
    {
        return (string) $this->getAttribute('slug');
    }

    public function getDomain(): ?string
    {
        return $this->getAttribute('domain');
    }

    public function getCountryId(): int
    {
        return (int) $this->getAttribute('country_id');
    }

    public function getCurrencyId(): int
    {
        return (int) $this->getAttribute('currency_id');
    }

    public function getIsActive(): bool
    {
        return (bool) $this->getAttribute('is_active');
    }

    public function getScrapingEnabled(): bool
    {
        return (bool) $this->getAttribute('scraping_enabled');
    }

    public function getLastScrapedAt(): ?Carbon
    {
        /** @var ?Carbon $lastScrapedAt */
        $lastScrapedAt = $this->getAttribute('last_scraped_at');

        return $lastScrapedAt;
    }
}
