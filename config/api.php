<?php

declare(strict_types=1);

return [
    'default_version' => env('API_DEFAULT_VERSION', 'v1'),
    'cache_services' => (bool) env('API_CACHE_SERVICES', true),
];
