<?php

namespace Orchestra\Testbench\Bootstrap;

use Illuminate\Config\Repository;
use Illuminate\Contracts\Config\Repository as RepositoryContract;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use Orchestra\Testbench\Foundation\Env;
use Symfony\Component\Finder\Finder;

/**
 * @internal
 *
 * @phpstan-type TLaravel \Illuminate\Contracts\Foundation\Application
 */
class LoadConfiguration
{
    /**
     * Bootstrap the given application.
     *
     * @param  TLaravel  $app
     * @return void
     */
    public function bootstrap(Application $app): void
    {
        $app->instance('config', $config = new Repository([]));

        $this->loadConfigurationFiles($app, $config);

        if (\is_null($config->get('database.connections.testing'))) {
            $config->set('database.connections.testing', [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'foreign_key_constraints' => Env::get('DB_FOREIGN_KEYS', false),
            ]);
        }

        if ($config->get('database.default') === 'sqlite' && ! file_exists($config->get('database.connections.sqlite.database'))) {
            $config->set('database.default', 'testing');
        }

        mb_internal_encoding('UTF-8');
    }

    /**
     * Load the configuration items from all of the files.
     *
     * @param  TLaravel  $app
     * @param  \Illuminate\Contracts\Config\Repository  $config
     * @return void
     */
    private function loadConfigurationFiles(Application $app, RepositoryContract $config): void
    {
        $this->extendsLoadedConfiguration(
            LazyCollection::make(static function () use ($app) {
                $path = is_dir($app->basePath('config'))
                    ? $app->basePath('config')
                    : realpath(__DIR__.'/../../laravel/config');

                if (\is_string($path)) {
                    foreach (Finder::create()->files()->name('*.php')->in($path) as $file) {
                        yield basename($file->getRealPath(), '.php') => $file->getRealPath();
                    }
                }
            })
                ->collect()
                ->transform(fn ($path, $key) => $this->resolveConfigurationFile($path, $key))
        )->each(static function ($path, $key) use ($config) {
            $config->set($key, require $path);
        });
    }

    /**
     * Resolve the configuration file.
     *
     * @param  string  $path
     * @param  string  $key
     * @return string
     */
    protected function resolveConfigurationFile(string $path, string $key): string
    {
        return $path;
    }

    /**
     * Extend the loaded configuration.
     *
     * @param  \Illuminate\Support\Collection  $configurations
     * @return \Illuminate\Support\Collection
     */
    protected function extendsLoadedConfiguration(Collection $configurations): Collection
    {
        return $configurations;
    }
}
