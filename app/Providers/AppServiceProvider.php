<?php

declare(strict_types=1);

namespace App\Providers;

use App\Listeners\RecordSuccessfulLogin;
use App\Models\General\Category\Category;
use App\Models\General\Country\Country;
use App\Models\Product\Product;
use App\Models\Retailer\Retailer;
use App\Models\User\User;
use App\Observers\UserObserver;
use App\Policies\General\Category\CategoryPolicy;
use App\Policies\General\Country\CountryPolicy;
use App\Policies\ProductPolicy;
use App\Policies\RetailerPolicy;
use Illuminate\Auth\Events\Login;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        User::observe(UserObserver::class);
        Event::listen(Login::class, RecordSuccessfulLogin::class);

        Gate::policy(Country::class, CountryPolicy::class);
        Gate::policy(Category::class, CategoryPolicy::class);
        Gate::policy(Retailer::class, RetailerPolicy::class);
        Gate::policy(Product::class, ProductPolicy::class);

        RateLimiter::for('auth-token', function (Request $request): array {
            $ip        = (string) $request->ip();
            $grantType = (string) $request->input('grant_type');

            if ($grantType === 'internal') {
                $login = Str::lower(trim((string) $request->input('login', 'guest')));

                return [
                    Limit::perMinute(5)->by('auth-account|'.$login),
                    Limit::perMinute(30)->by('auth-ip|'.$ip),
                ];
            }

            return [
                Limit::perMinute(30)->by('auth-client|'.(string) $request->input('client_id').'|'.$ip),
                Limit::perMinute(100)->by('auth-ip|'.$ip),
            ];
        });
    }
}
