<?php

declare(strict_types=1);

namespace App\Providers;

use App\CacheRepositories\General\Country\CountryCacheRepository;
use App\CacheRepositories\General\Currency\CurrencyCacheRepository;
use App\CacheRepositories\Tour\TourCacheRepository;
use App\Contracts\Repositories\Auth\AuthLoginAttemptRepositoryContract;
use App\Contracts\Repositories\Company\CompanyRepositoryContract;
use App\Contracts\Repositories\Company\CompanySettingRepositoryContract;
use App\Contracts\Repositories\General\Country\CountryRepositoryContract;
use App\Contracts\Repositories\General\Currency\CurrencyRepositoryContract;
use App\Contracts\Repositories\Tour\TourRepositoryContract;
use App\Contracts\Services\Company\CompanyServiceContract;
use App\Contracts\Services\Company\CompanySettingServiceContract;
use App\Contracts\Services\General\Country\CountryServiceContract;
use App\Contracts\Services\General\Currency\CurrencyServiceContract;
use App\Contracts\Services\Tour\TourServiceContract;
use App\Repositories\Auth\AuthLoginAttemptRepository;
use App\Repositories\Company\CompanyRepository;
use App\Repositories\Company\CompanySettingRepository;
use App\Repositories\General\Country\CountryRepository;
use App\Repositories\General\Currency\CurrencyRepository;
use App\Repositories\Tour\TourRepository;
use App\Services\Company\CompanyService;
use App\Services\Company\CompanySettingService;
use App\Services\General\Country\CountryService;
use App\Services\General\Currency\CurrencyService;
use App\Services\Tour\TourService;
use Illuminate\Support\ServiceProvider;

class BindingServiceProvider extends ServiceProvider
{
    private const array REPOSITORIES = [
        AuthLoginAttemptRepositoryContract::class => [
            'v1' => [
                AuthLoginAttemptRepository::class,
            ],
        ],
        CompanyRepositoryContract::class => [
            'v1' => [
                CompanyRepository::class,
            ],
        ],
        CompanySettingRepositoryContract::class => [
            'v1' => [
                CompanySettingRepository::class,
            ],
        ],
        CurrencyRepositoryContract::class => [
            'v1' => [
                CurrencyRepository::class,
                CurrencyCacheRepository::class,
            ],
        ],
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
        CompanyServiceContract::class => [
            'v1' => [
                CompanyService::class,
            ],
        ],
        CompanySettingServiceContract::class => [
            'v1' => [
                CompanySettingService::class,
            ],
        ],
        CurrencyServiceContract::class => [
            'v1' => [
                CurrencyService::class,
            ],
        ],
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
