<?php

declare(strict_types=1);

namespace App\Models\General;

use App\Enums\General\Month as MonthEnum;
use App\Models\Model;
use App\Models\Tour\Tour;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Month extends Model
{
    protected $table = 'months';

    /** @var list<string> */
    protected $fillable = ['key', 'display_name', 'sort_order'];

    protected function casts(): array
    {
        return [
            'key'        => MonthEnum::class,
            'sort_order' => 'integer',
        ];
    }

    public function getKeyValue(): MonthEnum
    {
        return $this->getAttribute('key');
    }

    public function getDisplayName(): string
    {
        return (string) $this->getAttribute('display_name');
    }

    public function getSortOrder(): int
    {
        return (int) $this->getAttribute('sort_order');
    }

    public function tours(): BelongsToMany
    {
        return $this->belongsToMany(Tour::class, 'tour_best_months');
    }
}
