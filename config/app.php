<?php

return [
    'name' => env('APP_NAME', 'Synapse Laravel Example'),
    'env' => env('APP_ENV', 'local'),
    'debug' => (bool) env('APP_DEBUG', true),
    'url' => env('APP_URL', 'http://localhost'),
    'timezone' => 'UTC',
    'locale' => 'en',
    'fallback_locale' => 'en',
    'faker_locale' => 'en_US',
    'key' => env('APP_KEY', 'base64:WpzG8QlVJE689HtiCWh3rIzUA3bKC4MgnZFjyDNkknI='),
    'cipher' => 'AES-256-CBC',
    'maintenance' => [
        'driver' => 'file',
    ],
    'providers' => [
        // No extra providers needed for API-only
    ],
];
