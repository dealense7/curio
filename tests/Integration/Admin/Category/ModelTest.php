<?php

declare(strict_types=1);

use App\Support\Testing\ProvidesTestingData;
use Tests\Integration\Admin\Category\ModelTestCase;

uses(ModelTestCase::class);

it('creates a category with a public id', function (): void {
    $category = ProvidesTestingData::createCategoryRandomItem()->firstOrFail();

    expect($category->getPublicId())->not->toBe('');
});
