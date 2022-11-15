<?php

namespace Orchestra\Testbench\Bootstrap;

use Illuminate\Contracts\Foundation\Application;
use Spatie\Ray\Settings\Settings;

/**
 * @phpstan-type TLaravel \Illuminate\Contracts\Foundation\Application
 */
class ConfigureRay
{
    /**
     * Bootstrap the given application.
     *
     * @param  TLaravel  $app
     * @return void
     */
    public function bootstrap(Application $app): void
    {
        $this->callAfterResolvingSettings($app, function ($settings, $app) {
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
     * @param  (callable(object, TLaravel):void)  $callback
     * @return void
     */
    protected function callAfterResolvingSettings(Application $app, callable $callback): void
    {
        $app->afterResolving(Settings::class, $callback);

        if ($app->resolved(Settings::class)) {
            $callback($app->make(Settings::class), $app);
        }
    }
}
