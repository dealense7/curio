<?php

declare(strict_types=1);

$allowedOrigins = array_values(array_filter(array_map(
    static fn (string $origin): string => trim($origin),
    explode(',', (string) env('CORS_ALLOWED_ORIGINS', 'http://localhost:5173')),
)));

return [
    'paths'                    => ['api/*'],
    'allowed_methods'          => ['*'],
    'allowed_origins'          => $allowedOrigins,
    'allowed_origins_patterns' => [],
    'allowed_headers'          => ['Accept', 'Authorization', 'Content-Type', 'Origin', 'X-Api-Version'],
    'exposed_headers'          => [],
    'max_age'                  => 600,
    'supports_credentials'     => false,
];
