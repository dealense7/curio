<?php

declare(strict_types=1);

use App\Support\Testing\ProvidesTestingData;
use Tests\Integration\Admin\Retailer\ModelTestCase;

uses(ModelTestCase::class);

it('creates a retailer with a public id and default flags', function (): void {
    $retailer = ProvidesTestingData::createRetailerRandomItem()->firstOrFail();

    expect($retailer->getPublicId())->not->toBe('')
        ->and($retailer->getIsActive())->toBeTrue()
        ->and($retailer->getScrapingEnabled())->toBeTrue();
});

it('provides retailer factory states', function (): void {
    expect(ProvidesTestingData::createRetailerRandomItem(['is_active' => false])->firstOrFail()->getIsActive())->toBeFalse()
        ->and(ProvidesTestingData::createRetailerRandomItem(['scraping_enabled' => false])->firstOrFail()->getScrapingEnabled())->toBeFalse();
});
