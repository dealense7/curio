<?php

declare(strict_types=1);

namespace App\Providers;

use App\CacheRepositories\General\Country\CountryCacheRepository;
use App\Contracts\Repositories\General\Country\CountryRepositoryContract;
use App\Contracts\Services\General\Country\CountryServiceContract;
use App\Repositories\General\Country\CountryRepository;
use App\Services\General\Country\CountryService;
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
    ];

    private const array SERVICES = [
        CountryServiceContract::class => [
            'v1' => [
                CountryService::class,
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
            $concrete = $cacheServices ? data_get($concretes, 1, $concretes[0]) : $concretes[0];

            $this->app->bind($abstract, $concrete);
        }
    }
}
