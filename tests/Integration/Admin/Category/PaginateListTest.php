<?php

declare(strict_types=1);

use App\Support\Testing\ProvidesTestingData;
use Tests\Integration\Admin\Category\ModelTestCase;

uses(ModelTestCase::class);

it('should raise unauthorized', function (): void {
    $response = $this->jsonWithHeader('GET', $this->url('admin/categories'));

    $response->assertUnauthorized();
});

it('should raise forbidden', function (): void {
    ProvidesTestingData::createRandomUserAndAuthorize();

    $response = $this->jsonWithHeader('GET', $this->url('admin/categories'));

    $response->assertForbiddenPermissions($this->getPermissions(['read']));
    $response->assertForbidden();
});

it('should paginate categories', function (): void {
    ProvidesTestingData::createRandomUserAndAuthorize([], [
        'permissions' => $this->getPermissions(['read']),
    ]);
    ProvidesTestingData::createCategoryRandomItem([], 5);

    $data = [
        'page'    => 2,
        'perPage' => 2,
    ];
    $response = $this->jsonWithHeader('GET', $this->url('admin/categories'), $data);

    $response->assertJsonDataCount(2);
    $response->assertJsonDataPagination([
        'page'    => 2,
        'perPage' => 2,
        'count'   => 2,
        'total'   => 5,
    ]);
});
