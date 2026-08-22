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

it('should return paginated products', function (array $data): void {
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
})->with('dataForProductPagination');

dataset('dataForProductPagination', static function (): array {
    return [
        'use-pagination' => [
            'data' => [
                'dataCallback' => static function (): void {
                    ProvidesTestingData::createProductRandomItem([], 5);
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
