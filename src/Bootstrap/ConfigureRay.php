<?php

namespace Orchestra\Testbench\Bootstrap;

use Closure;
use Illuminate\Contracts\Foundation\Application;
use Spatie\LaravelRay\Watchers\EventWatcher;
use Spatie\Ray\Settings\Settings;

/**
 * @internal
 *
 * @phpstan-type TLaravel \Illuminate\Contracts\Foundation\Application
 */
final class ConfigureRay
{
    /**
     * Bootstrap the given application.
     *
     * @param  TLaravel  $app
     * @return void
     */
    public function bootstrap(Application $app): void
    {
        $this->callAfterResolving($app, EventWatcher::class, function ($watcher) {
            /** @var \Spatie\LaravelRay\Watchers\EventWatcher $watcher */
            $watcher->disable();
        });

        $this->callAfterResolving($app, Settings::class, function ($settings, $app) {
            /** @var \Spatie\Ray\Settings\Settings $settings */
            /** @var \Illuminate\Contracts\Config\Repository $config */
            $config = $app->make('config');

            if ($config->get('database.default') === 'sqlite' && ! file_exists($config->get('database.connections.sqlite.database'))) {
                $settings->send_queries_to_ray = false;
                $settings->send_duplicate_queries_to_ray = false;
                $settings->send_slow_queries_to_ray = false;
            }
        });
    }

    /**
     * Setup an after resolving listener, or fire immediately if already resolved.
     *
     * @param  TLaravel  $app
     * @param  class-string  $class
     * @param  \Closure(\Spatie\Ray\Settings\Settings, TLaravel):void  $callback
     * @return void
     */
    protected function callAfterResolving(Application $app, $class, Closure $callback): void
    {
        $app->afterResolving($class, $callback);

        if ($app->resolved($class)) {
            $callback($app->make($class), $app);
        }
    }
}
