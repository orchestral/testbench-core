<?php

namespace Workbench\App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class WorkbenchServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->loadMigrationsFrom(realpath(__DIR__.'/../../database/migrations'));

        Route::macro('text', function (string $url, string $content) {
            return $this->get($url, fn () => response($content)->header('Content-Type', 'text/plain'));
        });
    }
}
