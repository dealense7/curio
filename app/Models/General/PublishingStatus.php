<?php

declare(strict_types=1);

namespace App\Models\General;

use App\Enums\General\PublishingStatus as PublishingStatusEnum;
use App\Models\Model;
use App\Models\Tour\Tour;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PublishingStatus extends Model
{
    protected $table = 'publishing_statuses';

    /** @var list<string> */
    protected $fillable = ['key', 'display_name'];

    protected function casts(): array
    {
        return ['key' => PublishingStatusEnum::class];
    }

    public function getKeyValue(): PublishingStatusEnum
    {
        return $this->getAttribute('key');
    }

    public function getDisplayName(): string
    {
        return (string) $this->getAttribute('display_name');
    }

    public function tours(): HasMany
    {
        return $this->hasMany(Tour::class, 'publishing_status_id');
    }
}
