<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

use function Orchestra\Testbench\default_skeleton_path;

/**
 * The first thing we will do is create a new Laravel application instance
 * which serves as the brain for all of the Laravel components. We will
 * also use the application to configure core, foundational behavior.
 */

return Application::configure($APP_BASE_PATH ?? default_skeleton_path())
    ->withProviders()
    ->withRouting(
        web: __DIR__.'/web.php',
        // api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        // channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
