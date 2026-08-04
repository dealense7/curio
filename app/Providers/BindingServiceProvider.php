<?php

declare(strict_types=1);

namespace App\Providers;

use App\CacheRepositories\General\Country\CountryCacheRepository;
use App\CacheRepositories\Tour\TourCacheRepository;
use App\Contracts\Repositories\General\Country\CountryRepositoryContract;
use App\Contracts\Repositories\Tour\TourRepositoryContract;
use App\Contracts\Services\General\Country\CountryServiceContract;
use App\Contracts\Services\Tour\TourServiceContract;
use App\Repositories\General\Country\CountryRepository;
use App\Repositories\Tour\TourRepository;
use App\Services\General\Country\CountryService;
use App\Services\Tour\TourService;
use Illuminate\Support\ServiceProvider;

class BindingServiceProvider extends ServiceProvider
{
    private const array REPOSITORIES = [
        CountryRepositoryContract::class => [
            'v1' => [
                CountryRepository::class,
                CountryCacheRepository::class,
            ],
        ],
        TourRepositoryContract::class => [
            'v1' => [
                TourRepository::class,
                TourCacheRepository::class,
            ],
        ],
    ];

    private const array SERVICES = [
        CountryServiceContract::class => [
            'v1' => [
                CountryService::class,
            ],
        ],
        TourServiceContract::class => [
            'v1' => [
                TourService::class,
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
