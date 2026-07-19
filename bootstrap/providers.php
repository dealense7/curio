<?php

use App\Providers\AppServiceProvider;
use App\Providers\BindingServiceProvider;
use App\Providers\ModelConventionServiceProvider;
use App\Providers\PassportServiceProvider;

return [
    AppServiceProvider::class,
    BindingServiceProvider::class,
    ModelConventionServiceProvider::class,
    PassportServiceProvider::class,
];
