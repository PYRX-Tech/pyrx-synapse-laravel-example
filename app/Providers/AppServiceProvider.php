<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('files', fn () => new \Illuminate\Filesystem\Filesystem);
    }

    public function boot(): void
    {
        //
    }
}
