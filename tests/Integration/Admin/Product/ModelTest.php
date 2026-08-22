<?php

declare(strict_types=1);

use App\Models\General\File;
use App\Models\Product\Product;
use App\Support\Testing\ProvidesTestingData;
use Tests\Integration\Admin\Product\ModelTestCase;

uses(ModelTestCase::class);

it('creates a product with a public id', function (): void {
    $product = ProvidesTestingData::createProductRandomItem()->firstOrFail();
    expect($product->getPublicId())->not->toBe('');
});

it('loads product files through the polymorphic relationship', function (): void {
    $product = ProvidesTestingData::createProductRandomItem()->firstOrFail();

    ProvidesTestingData::createFileRandomItem([
        'fileable_type' => Product::class,
        'fileable_id'   => $product->getId(),
    ]);

    expect($product->files()->first())->toBeInstanceOf(File::class);
});

it('returns typed product field values through getters', function (): void {
    $product = ProvidesTestingData::createProductRandomItem([
        'category_id' => null,
        'name'        => 'Example Product',
        'brand'       => null,
        'gtin'        => null,
        'size_value'  => null,
        'size_unit'   => null,
        'pack_count'  => null,
        'description' => null,
        'is_active'   => false,
    ])->firstOrFail();

    expect($product->getCategoryId())->toBeNull()
        ->and($product->getName())->toBe('Example Product')
        ->and($product->getBrand())->toBeNull()
        ->and($product->getGtin())->toBeNull()
        ->and($product->getSizeValue())->toBeNull()
        ->and($product->getSizeUnit())->toBeNull()
        ->and($product->getPackCount())->toBeNull()
        ->and($product->getDescription())->toBeNull()
        ->and($product->getIsActive())->toBeFalse();
});
