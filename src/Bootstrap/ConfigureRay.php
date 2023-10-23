<?php

namespace Orchestra\Testbench\Bootstrap;

use Illuminate\Contracts\Foundation\Application;
use PHPUnit\Framework\Attributes\IgnoreMethodForCodeCoverage;
use Spatie\Ray\Settings\Settings;

use function Orchestra\Testbench\after_resolving;

/**
 * @internal
 *
 * @phpstan-type TLaravel \Illuminate\Contracts\Foundation\Application
 */
#[IgnoreMethodForCodeCoverage(ConfigureRay::class, 'bootstrap')]
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
        after_resolving($app, Settings::class, static function ($settings, $app) {
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
}
