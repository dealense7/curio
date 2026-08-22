<?php

declare(strict_types=1);

namespace App\Providers;

use App\CacheRepositories\General\Category\CategoryCacheRepository;
use App\CacheRepositories\General\Country\CountryCacheRepository;
use App\CacheRepositories\General\Currency\CurrencyCacheRepository;
use App\CacheRepositories\Product\ProductCacheRepository;
use App\CacheRepositories\Retailer\RetailerCacheRepository;
use App\Contracts\Repositories\Auth\AuthLoginAttemptRepositoryContract;
use App\Contracts\Repositories\General\Category\CategoryRepositoryContract;
use App\Contracts\Repositories\General\Country\CountryRepositoryContract;
use App\Contracts\Repositories\General\Currency\CurrencyRepositoryContract;
use App\Contracts\Repositories\Product\ProductRepositoryContract;
use App\Contracts\Repositories\Retailer\RetailerRepositoryContract;
use App\Contracts\Services\General\Category\CategoryServiceContract;
use App\Contracts\Services\General\Country\CountryServiceContract;
use App\Contracts\Services\General\Currency\CurrencyServiceContract;
use App\Contracts\Services\Product\ProductServiceContract;
use App\Contracts\Services\Retailer\RetailerServiceContract;
use App\Repositories\Auth\AuthLoginAttemptRepository;
use App\Repositories\General\Category\CategoryRepository;
use App\Repositories\General\Country\CountryRepository;
use App\Repositories\General\Currency\CurrencyRepository;
use App\Repositories\Product\ProductRepository;
use App\Repositories\Retailer\RetailerRepository;
use App\Services\General\Category\CategoryService;
use App\Services\General\Country\CountryService;
use App\Services\General\Currency\CurrencyService;
use App\Services\Product\ProductService;
use App\Services\Retailer\RetailerService;
use Illuminate\Support\ServiceProvider;

class BindingServiceProvider extends ServiceProvider
{
    private const array REPOSITORIES = [
        AuthLoginAttemptRepositoryContract::class => [
            'v1' => [
                AuthLoginAttemptRepository::class,
            ],
        ],
        CurrencyRepositoryContract::class         => [
            'v1' => [
                CurrencyRepository::class,
                CurrencyCacheRepository::class,
            ],
        ],
        CountryRepositoryContract::class          => [
            'v1' => [
                CountryRepository::class,
                CountryCacheRepository::class,
            ],
        ],
        RetailerRepositoryContract::class         => [
            'v1' => [
                RetailerRepository::class,
                RetailerCacheRepository::class,
            ],
        ],
        ProductRepositoryContract::class          => [
            'v1' => [
                ProductRepository::class,
                ProductCacheRepository::class,
            ],
        ],
        CategoryRepositoryContract::class         => [
            'v1' => [
                CategoryRepository::class,
                CategoryCacheRepository::class,
            ],
        ],
    ];

    private const array SERVICES = [
        CurrencyServiceContract::class => [
            'v1' => [
                CurrencyService::class,
            ],
        ],
        CountryServiceContract::class  => [
            'v1' => [
                CountryService::class,
            ],
        ],
        RetailerServiceContract::class => [
            'v1' => [
                RetailerService::class,
            ],
        ],
        ProductServiceContract::class  => [
            'v1' => [
                ProductService::class,
            ],
        ],
        CategoryServiceContract::class => [
            'v1' => [
                CategoryService::class,
            ],
        ],
    ];

    private const array REQUESTS = [];

    public function register(): void
    {
        $version = strtolower((string) $this->app['request']->header(
            'X-Api-Version',
            config('api.default_version', 'v1'),
        ));

        $cacheServices = (bool) config('api.cache_services', true);

        $bindings = [
            ...self::REPOSITORIES,
            ...self::SERVICES,
            ...self::REQUESTS,
        ];

        foreach ($bindings as $abstract => $versions) {
            $concretes = $versions[$version] ?? $versions['v1'];
            $concrete  = $cacheServices ? data_get($concretes, 1, $concretes[0]) : $concretes[0];

            $this->app->bind($abstract, $concrete);
        }
    }
}
