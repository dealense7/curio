<?php

declare(strict_types=1);

use App\Support\Testing\ProvidesTestingData;
use Illuminate\Support\Arr;
use Tests\Integration\Admin\Product\ModelTestCase;

uses(ModelTestCase::class);

it('should raise unauthorized', function (): void {
    $response = $this->jsonWithHeader('GET', $this->url('admin/products'));

    $response->assertUnauthorized();
});

it('should raise forbidden', function (): void {
    ProvidesTestingData::createRandomUserAndAuthorize();

    $response = $this->jsonWithHeader('GET', $this->url('admin/products'));

    $response->assertForbiddenPermissions($this->getPermissions(['read']));
    $response->assertForbidden();
});

it('should return product list', function (array $data): void {
    ProvidesTestingData::createRandomUserAndAuthorize([], [
        'permissions' => $this->getPermissions(['read']),
    ]);

    $data['dataCallback']();

    $response = $this->jsonWithHeader(
        'GET',
        $this->url('admin/products'),
        Arr::get($data, 'request', []),
    );

    $response->assertOk();
    $response->assertJsonDataPagination(Arr::get($data, 'response'));
    $response->assertJsonDataCollectionStructure($this->getProductStructure());
})->with('dataForProductListing');

it('should filter products by category', function (): void {
    ProvidesTestingData::createRandomUserAndAuthorize([], [
        'permissions' => $this->getPermissions(['read']),
    ]);
    $target = ProvidesTestingData::createCategoryRandomItem()->firstOrFail();
    $other  = ProvidesTestingData::createCategoryRandomItem()->firstOrFail();
    ProvidesTestingData::createProductRandomItem(['category_id' => $target->getId()]);
    ProvidesTestingData::createProductRandomItem(['category_id' => $other->getId()]);

    $data = [
        'filters' => [
            'category_id' => $target->getPublicId(),
        ],
    ];
    $response = $this->jsonWithHeader('GET', $this->url('admin/products'), $data);

    $response->assertJsonDataCount(1);
    $response->assertJsonDataCollectionStructure($this->getProductStructure());
});

it('should return no products for an unknown category filter', function (): void {
    ProvidesTestingData::createRandomUserAndAuthorize([], [
        'permissions' => $this->getPermissions(['read']),
    ]);
    ProvidesTestingData::createProductRandomItem();

    $data = [
        'filters' => [
            'category_id' => '01J00000000000000000000000',
        ],
    ];
    $response = $this->jsonWithHeader('GET', $this->url('admin/products'), $data);

    $response->assertJsonDataCount(0);
});

dataset('dataForProductListing', static function (): array {
    return [
        'filters-empty' => [
            'data' => [
                'dataCallback' => static function (): void {
                    ProvidesTestingData::createProductRandomItem([], 3);
                },
                'request'  => ['filters' => []],
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
                    ProvidesTestingData::createProductRandomItem(['name' => 'Other Product']);
                    ProvidesTestingData::createProductRandomItem(['name' => 'Target Product']);
                },
                'request'  => ['filters' => ['name' => 'Target']],
                'response' => [
                    'page'    => 1,
                    'perPage' => 15,
                    'count'   => 1,
                    'total'   => 1,
                ],
            ],
        ],
        'filters-brand' => [
            'data' => [
                'dataCallback' => static function (): void {
                    ProvidesTestingData::createProductRandomItem(['brand' => 'Other Brand']);
                    ProvidesTestingData::createProductRandomItem(['brand' => 'Target Brand']);
                },
                'request'  => ['filters' => ['brand' => 'Target']],
                'response' => [
                    'page'    => 1,
                    'perPage' => 15,
                    'count'   => 1,
                    'total'   => 1,
                ],
            ],
        ],
        'filters-gtin' => [
            'data' => [
                'dataCallback' => static function (): void {
                    ProvidesTestingData::createProductRandomItem(['gtin' => '5449000131836']);
                    ProvidesTestingData::createProductRandomItem(['gtin' => '01234567']);
                },
                'request'  => ['filters' => ['gtin' => '5449000131836']],
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
                    ProvidesTestingData::createProductRandomItem(['is_active' => true]);
                    ProvidesTestingData::createProductRandomItem(['is_active' => false]);
                },
                'request'  => ['filters' => ['is_active' => true]],
                'response' => [
                    'page'    => 1,
                    'perPage' => 15,
                    'count'   => 1,
                    'total'   => 1,
                ],
            ],
        ],
    ];
});
