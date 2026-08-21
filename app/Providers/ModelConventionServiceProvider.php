<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\ServiceProvider;

class ModelConventionServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap model strictness and schema helpers for repository conventions.
     */
    public function boot(): void
    {
        Model::shouldBeStrict(! app()->isProduction());

        Blueprint::macro('publicId', function (): Blueprint {
            /** @var Blueprint $this */
            $this->ulid('public_id')->unique();

            return $this;
        });

        Blueprint::macro('companyKey', function (bool $nullable = false): Blueprint {
            /** @var Blueprint $this */
            $column = $nullable
                ? $this->foreignId('company_id')->nullable()
                : $this->foreignId('company_id');

            $column->constrained('companies')->restrictOnDelete();

            return $this;
        });

        Blueprint::macro('archivable', function (): Blueprint {
            /** @var Blueprint $this */
            $this->timestampTz('archived_at')->nullable()->index();

            return $this;
        });

        Blueprint::macro('optimisticLock', function (int $default = 1): Blueprint {
            /** @var Blueprint $this */
            $this->unsignedInteger('version')->default($default);

            return $this;
        });

        Blueprint::macro('actorColumns', function (): Blueprint {
            /** @var Blueprint $this */
            $this->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $this->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            return $this;
        });

        Blueprint::macro('enumString', function (string $column, array $values, ?string $default = null): Blueprint {
            /** @var Blueprint $this */
            $definition = $this->string($column, 64);

            if ($default !== null) {
                $definition->default($default);
            }

            return $this;
        });

        Blueprint::macro('money', function (string $name): Blueprint {
            /** @var Blueprint $this */
            $this->unsignedBigInteger($name.'_amount_minor');
            $this->char($name.'_currency_code', 3);

            return $this;
        });

        Blueprint::macro('coordinates', function (string $latitude = 'latitude', string $longitude = 'longitude'): Blueprint {
            /** @var Blueprint $this */
            $this->decimal($latitude, 10, 7);
            $this->decimal($longitude, 10, 7);

            return $this;
        });

        Blueprint::macro('weight', function (string $column = 'weight'): Blueprint {
            /** @var Blueprint $this */
            $this->decimal($column, 12, 3);

            return $this;
        });

        Blueprint::macro('dimensions', function (string $prefix = ''): Blueprint {
            /** @var Blueprint $this */
            $prefix = $prefix === '' ? '' : $prefix.'_';
            $this->decimal($prefix.'length', 10, 2);
            $this->decimal($prefix.'width', 10, 2);
            $this->decimal($prefix.'height', 10, 2);

            return $this;
        });
    }
}
