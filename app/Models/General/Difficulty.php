<?php

declare(strict_types=1);

namespace App\Models\General;

use App\Enums\General\Difficulty as DifficultyEnum;
use App\Models\Model;
use App\Models\Tour\Tour;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Difficulty extends Model
{
    protected $table = 'difficulties';

    /** @var list<string> */
    protected $fillable = ['key', 'display_name'];

    protected function casts(): array
    {
        return ['key' => DifficultyEnum::class];
    }

    public function getKeyValue(): DifficultyEnum
    {
        return $this->getAttribute('key');
    }

    public function getDisplayName(): string
    {
        return (string) $this->getAttribute('display_name');
    }

    public function tours(): HasMany
    {
        return $this->hasMany(Tour::class, 'difficulty_id');
    }
}
