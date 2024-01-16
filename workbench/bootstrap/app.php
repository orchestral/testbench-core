<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

use function Illuminate\Filesystem\join_paths;

/**
 * The first thing we will do is create a new Laravel application instance
 * which serves as the brain for all of the Laravel components. We will
 * also use the application to configure core, foundational behavior.
 */

return Application::configure((string) realpath(join_paths(__DIR__, '..', '..', 'laravel')))
    ->withProviders()
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        // api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        // channels: __DIR__.'/../routes/channels.php',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
