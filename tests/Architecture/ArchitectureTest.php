<?php

declare(strict_types=1);

use App\Models\Model;

arch('application code uses strict types')
    ->expect('App')
    ->toUseStrictTypes();

arch('application does not use debug functions')
    ->expect('App')
    ->not
    ->toUse(['dd', 'dump', 'die', 'var_dump']);

arch('shared model traits are traits')
    ->expect('App\Support\Traits')
    ->toBeTraits();

arch('general file is an application model')
    ->expect('App\Models\General\File')
    ->toBeClasses()
    ->toExtend(Model::class);

arch('admin controllers do not bypass services')
    ->expect('App\Http\Controllers\Admin')
    ->not
    ->toUse(['App\Services', 'App\Repositories', 'App\CacheRepositories']);

arch('services do not bypass repository contracts')
    ->expect('App\Services')
    ->not
    ->toUse(['App\Repositories', 'App\CacheRepositories'])
    ->ignoring('App\Services\General\Country');

arch('admin controllers follow naming conventions')
    ->expect('App\Http\Controllers\Admin')
    ->toHaveSuffix('Controller');
