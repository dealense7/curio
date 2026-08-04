<?php

declare(strict_types=1);

namespace App\Models\General;

use App\Enums\General\Currency as CurrencyEnum;
use App\Models\Model;
use App\Models\Tour\Tour;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Currency extends Model
{
    protected $table = 'currencies';

    /** @var list<string> */
    protected $fillable = ['key', 'display_name'];

    protected function casts(): array
    {
        return ['key' => CurrencyEnum::class];
    }

    public function getKeyValue(): CurrencyEnum
    {
        return $this->getAttribute('key');
    }

    public function getDisplayName(): string
    {
        return (string) $this->getAttribute('display_name');
    }

    public function tours(): HasMany
    {
        return $this->hasMany(Tour::class, 'currency_id');
    }
}
