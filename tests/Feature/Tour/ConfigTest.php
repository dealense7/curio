<?php

declare(strict_types=1);

use App\Enums\General\Currency;
use App\Enums\General\Difficulty;
use App\Enums\General\Month;
use App\Enums\General\PublishingStatus;
use App\Support\Testing\ProvidesTestingData;
use Tests\Feature\Tour\ModelTestCase;

uses(ModelTestCase::class);

it('should return generated lookup ids and enum keys', function (): void {
    foreach (Difficulty::cases() as $difficulty) {
        ProvidesTestingData::createDifficultyRandomItem(['key' => $difficulty, 'display_name' => $difficulty->getText()]);
    }

    foreach (PublishingStatus::cases() as $status) {
        ProvidesTestingData::createPublishingStatusRandomItem(['key' => $status, 'display_name' => $status->getText()]);
    }

    foreach (Currency::cases() as $currency) {
        ProvidesTestingData::createCurrencyRandomItem(['key' => $currency, 'display_name' => $currency->getText()]);
    }

    foreach (Month::cases() as $index => $month) {
        ProvidesTestingData::createMonthRandomItem([
            'key'          => $month,
            'display_name' => $month->getText(),
            'sort_order'   => $index + 1,
        ]);
    }

    $response = $this->jsonWithHeader('GET', $this->url('general/tours/config'));

    $response->assertOk();
    $response->assertJsonDataItemStructure($this->getTourConfigStructure());
    $response->assertJsonCount(count(Difficulty::cases()), 'data.difficulties');
    $response->assertJsonCount(count(PublishingStatus::cases()), 'data.publishing_statuses');
    $response->assertJsonCount(count(Currency::cases()), 'data.currencies');
    $response->assertJsonCount(count(Month::cases()), 'data.months');

    expect($response->json('data.difficulties.0.id'))->toBeInt();
});
