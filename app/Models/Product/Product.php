<?php

declare(strict_types=1);

namespace App\Models\Product;

use App\Models\General\Category\Category;
use App\Models\General\File;
use App\Models\Model;
use App\Support\Resources\Contracts\UuidAsPrimaryContract;
use App\Support\Traits\HasPublicId;
use Database\Factories\Product\ProductFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[UseFactory(ProductFactory::class)]
class Product extends Model implements UuidAsPrimaryContract
{
    use HasPublicId;
    use SoftDeletes;

    public const string PERMISSIONS_SCOPE = 'products';

    protected array $sortFields = [
        'name',
        'brand',
        'gtin',
        'is_active',
        'created_at',
        'updated_at',
    ];

    protected array $sortBy = ['name' => 'asc'];

    protected $table = 'products';

    protected $fillable = [
        'category_id',
        'name',
        'brand',
        'gtin',
        'size_value',
        'size_unit',
        'pack_count',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'category_id' => 'integer',
            'size_value'  => 'decimal:3',
            'pack_count'  => 'integer',
            'is_active'   => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * The File model is supplied by the existing file subsystem.
     */
    public function files(): MorphMany
    {
        return $this->morphMany(File::class, 'fileable');
    }

    public function getCategoryId(): ?int
    {
        $categoryId = $this->getAttribute('category_id');

        return $categoryId === null ? null : (int) $categoryId;
    }

    public function getName(): string
    {
        return (string) $this->getAttribute('name');
    }

    public function getBrand(): ?string
    {
        $brand = $this->getAttribute('brand');

        return $brand === null ? null : (string) $brand;
    }

    public function getGtin(): ?string
    {
        $gtin = $this->getAttribute('gtin');

        return $gtin === null ? null : (string) $gtin;
    }

    public function getSizeValue(): ?string
    {
        $sizeValue = $this->getAttribute('size_value');

        return $sizeValue === null ? null : (string) $sizeValue;
    }

    public function getSizeUnit(): ?string
    {
        $sizeUnit = $this->getAttribute('size_unit');

        return $sizeUnit === null ? null : (string) $sizeUnit;
    }

    public function getPackCount(): ?int
    {
        $packCount = $this->getAttribute('pack_count');

        return $packCount === null ? null : (int) $packCount;
    }

    public function getDescription(): ?string
    {
        $description = $this->getAttribute('description');

        return $description === null ? null : (string) $description;
    }

    public function getIsActive(): bool
    {
        return (bool) $this->getAttribute('is_active');
    }
}
