<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();

// Register filesystem early — required by EventServiceProvider on PHP 8.5
if (!$app->bound('files')) {
    $app->singleton('files', fn () => new \Illuminate\Filesystem\Filesystem);
}

return $app;
