<?php

declare(strict_types=1);

namespace App\Models\Category;

use App\Models\Concerns\HasPublicId;
use App\Models\Model;
use App\Support\Resources\Contracts\UuidAsPrimaryContract;
use Database\Factories\Category\CategoryFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[UseFactory(CategoryFactory::class)]
class Category extends Model implements UuidAsPrimaryContract
{
    use HasPublicId;
    use SoftDeletes;

    public const string PERMISSIONS_SCOPE = 'categories';

    /** @var list<string> */
    protected array $sortFields = ['name', 'slug', 'created_at', 'updated_at'];

    /** @var array<string, string> */
    protected array $sortBy = ['name' => 'asc'];

    protected $table = 'categories';

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
    ];

    protected function casts(): array
    {
        return [
            'parent_id' => 'integer',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('name');
    }

    public function getName(): string
    {
        return (string) $this->getAttribute('name');
    }

    public function getSlug(): string
    {
        return (string) $this->getAttribute('slug');
    }

    public function getParentId(): ?int
    {
        $parentId = $this->getAttribute('parent_id');

        return $parentId === null ? null : (int) $parentId;
    }
}
