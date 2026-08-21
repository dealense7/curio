<?php

declare(strict_types=1);

namespace App\Models\General;

use App\Enums\General\Currency as CurrencyEnum;
use App\Models\Concerns\HasPublicId;
use App\Models\Model;
use App\Models\Tour\Tour;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Currency extends Model
{
    use HasPublicId;

    protected $table = 'currencies';

    /** @var list<string> */
    protected $fillable = ['code', 'name', 'symbol', 'decimal_places', 'is_active', 'sort_order'];

    protected function casts(): array
    {
        return [
            'code'           => CurrencyEnum::class,
            'decimal_places' => 'integer',
            'is_active'      => 'boolean',
            'sort_order'     => 'integer',
        ];
    }

    public function getCodeValue(): CurrencyEnum
    {
        return $this->getAttribute('code');
    }

    public function getName(): string
    {
        return (string) $this->getAttribute('name');
    }

    public function tours(): HasMany
    {
        return $this->hasMany(Tour::class, 'currency_id');
    }
}
