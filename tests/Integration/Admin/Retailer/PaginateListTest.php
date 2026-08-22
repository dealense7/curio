<?php

declare(strict_types=1);

use App\Support\Testing\ProvidesTestingData;
use Illuminate\Support\Arr;
use Tests\Integration\Admin\Retailer\ModelTestCase;

uses(ModelTestCase::class);

it('should raise unauthorized', function (): void {
    $response = $this->jsonWithHeader('GET', $this->url('admin/retailers'));

    $response->assertUnauthorized();
});

it('should raise forbidden', function (): void {
    ProvidesTestingData::createRandomUserAndAuthorize();

    $response = $this->jsonWithHeader('GET', $this->url('admin/retailers'));

    $response->assertForbiddenPermissions($this->getPermissions(['read']));
    $response->assertForbidden();
});

it('should return retailer list', function (array $data): void {
    ProvidesTestingData::createRandomUserAndAuthorize([], [
        'permissions' => $this->getPermissions(['read']),
    ]);

    $data['dataCallback']();

    $response = $this->jsonWithHeader(
        'GET',
        $this->url('admin/retailers'),
        Arr::get($data, 'request', []),
    );

    $response->assertOk()
        ->assertJsonDataPagination(Arr::get($data, 'response'))
        ->assertJsonDataCollectionStructure($this->getRetailerStructure());
})->with('dataForRetailerListing');

dataset('dataForRetailerListing', static function (): array {
    return [
        'filters-empty' => [
            'data' => [
                'dataCallback' => static function (): void {
                    ProvidesTestingData::createRetailerRandomItem([], 3);
                },
                'request' => [
                    'filters' => [],
                ],
                'response' => [
                    'page'    => 1,
                    'perPage' => 15,
                    'count'   => 3,
                    'total'   => 3,
                ],
            ],
        ],
        'filters-name' => [
            'data' => [
                'dataCallback' => static function (): void {
                    ProvidesTestingData::createRetailerRandomItem(['name' => 'Other Retailer']);
                    ProvidesTestingData::createRetailerRandomItem(['name' => 'Target Retailer']);
                },
                'request' => [
                    'filters' => [
                        'name' => 'Target',
                    ],
                ],
                'response' => [
                    'page'    => 1,
                    'perPage' => 15,
                    'count'   => 1,
                    'total'   => 1,
                ],
            ],
        ],
        'filters-active' => [
            'data' => [
                'dataCallback' => static function (): void {
                    ProvidesTestingData::createRetailerRandomItem(['is_active' => true]);
                    ProvidesTestingData::createRetailerRandomItem(['is_active' => false]);
                },
                'request' => [
                    'filters' => [
                        'is_active' => true,
                    ],
                ],
                'response' => [
                    'page'    => 1,
                    'perPage' => 15,
                    'count'   => 1,
                    'total'   => 1,
                ],
            ],
        ],
        'use-pagination' => [
            'data' => [
                'dataCallback' => static function (): void {
                    ProvidesTestingData::createRetailerRandomItem([], 5);
                },
                'request' => [
                    'page'    => 2,
                    'perPage' => 2,
                ],
                'response' => [
                    'page'    => 2,
                    'perPage' => 2,
                    'count'   => 2,
                    'total'   => 5,
                ],
            ],
        ],
    ];
});
