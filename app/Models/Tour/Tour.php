<?php

declare(strict_types=1);

namespace App\Models\Tour;

use App\Models\Concerns\HasPublicId;
use App\Models\General\Currency;
use App\Models\General\Difficulty;
use App\Models\General\File;
use App\Models\General\Month;
use App\Models\General\PublishingStatus;
use App\Models\Model;
use App\Support\Resources\Contracts\UuidAsPrimaryContract;
use Database\Factories\TourFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[UseFactory(TourFactory::class)]
class Tour extends Model implements UuidAsPrimaryContract
{
    use HasPublicId;
    use SoftDeletes;

    public const string PERMISSIONS_SCOPE = 'tours';

    protected $table = 'tours';

    /** @var list<string> */
    protected $fillable = [
        'title',
        'description',
        'start_location',
        'end_location',
        'distance_km',
        'duration_comfortable_days',
        'duration_recommended_days',
        'daily_distance_comfortable_km',
        'daily_distance_recommended_km',
        'elevation_gain_m',
        'difficulty_id',
        'price_comfortable',
        'price_recommended',
        'average_daily_spend',
        'currency_id',
        'route_file_id',
        'cover_image_id',
        'publishing_status_id',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'distance_km'                   => 'decimal:2',
            'duration_comfortable_days'     => 'integer',
            'duration_recommended_days'     => 'integer',
            'daily_distance_comfortable_km' => 'decimal:2',
            'daily_distance_recommended_km' => 'decimal:2',
            'elevation_gain_m'              => 'integer',
            'difficulty_id'                 => 'integer',
            'price_comfortable'             => 'integer',
            'price_recommended'             => 'integer',
            'average_daily_spend'           => 'integer',
            'currency_id'                   => 'integer',
            'route_file_id'                 => 'integer',
            'cover_image_id'                => 'integer',
            'publishing_status_id'          => 'integer',
        ];
    }

    public function difficulty(): BelongsTo
    {
        return $this->belongsTo(Difficulty::class, 'difficulty_id');
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function publishingStatus(): BelongsTo
    {
        return $this->belongsTo(PublishingStatus::class, 'publishing_status_id');
    }

    public function routeFile(): BelongsTo
    {
        return $this->belongsTo(File::class, 'route_file_id');
    }

    public function coverImage(): BelongsTo
    {
        return $this->belongsTo(File::class, 'cover_image_id');
    }

    public function bestMonths(): BelongsToMany
    {
        return $this->belongsToMany(Month::class, 'tour_best_months')->orderBy('sort_order');
    }

    public function getTitle(): string
    {
        return (string) $this->getAttribute('title');
    }

    public function getDescription(): string
    {
        return (string) $this->getAttribute('description');
    }

    public function getStartLocation(): string
    {
        return (string) $this->getAttribute('start_location');
    }

    public function getEndLocation(): string
    {
        return (string) $this->getAttribute('end_location');
    }

    public function getDistanceKm(): string
    {
        return (string) $this->getAttribute('distance_km');
    }

    public function getDurationComfortableDays(): int
    {
        return (int) $this->getAttribute('duration_comfortable_days');
    }

    public function getDurationRecommendedDays(): int
    {
        return (int) $this->getAttribute('duration_recommended_days');
    }

    public function getDailyDistanceComfortableKm(): string
    {
        return (string) $this->getAttribute('daily_distance_comfortable_km');
    }

    public function getDailyDistanceRecommendedKm(): string
    {
        return (string) $this->getAttribute('daily_distance_recommended_km');
    }

    public function getElevationGainM(): int
    {
        return (int) $this->getAttribute('elevation_gain_m');
    }

    public function getDifficultyId(): int
    {
        return (int) $this->getAttribute('difficulty_id');
    }

    public function getPriceComfortable(): int
    {
        return (int) $this->getAttribute('price_comfortable');
    }

    public function getPriceRecommended(): int
    {
        return (int) $this->getAttribute('price_recommended');
    }

    public function getAverageDailySpend(): int
    {
        return (int) $this->getAttribute('average_daily_spend');
    }

    public function getCurrencyId(): int
    {
        return (int) $this->getAttribute('currency_id');
    }

    public function getRouteFileId(): int
    {
        return (int) $this->getAttribute('route_file_id');
    }

    public function getCoverImageId(): int
    {
        return (int) $this->getAttribute('cover_image_id');
    }

    public function getPublishingStatusId(): int
    {
        return (int) $this->getAttribute('publishing_status_id');
    }
}
