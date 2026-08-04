<?php

declare(strict_types=1);

use App\Enums\General\Month as MonthEnum;
use App\Models\General\Month;
use App\Models\Tour\Tour;
use App\Support\Testing\ProvidesTestingData;
use Illuminate\Database\QueryException;
use Tests\Feature\Tour\ModelTestCase;

uses(ModelTestCase::class);

it('should reject duplicate best months for the same tour', function (): void {
    /** @var Tour $tour */
    $tour  = ProvidesTestingData::createTourRandomItem()->first();
    /** @var Month $month */
    $month = ProvidesTestingData::createMonthRandomItem(['key' => MonthEnum::JUNE, 'sort_order' => 6])->first();
    $tour->bestMonths()->attach($month->getId());

    expect(fn () => $tour->bestMonths()->attach($month->getId()))
        ->toThrow(QueryException::class);
});
