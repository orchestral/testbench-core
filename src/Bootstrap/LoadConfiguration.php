<?php

namespace Orchestra\Testbench\Bootstrap;

use Illuminate\Config\Repository;
use Illuminate\Contracts\Config\Repository as RepositoryContract;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\LazyCollection;
use Orchestra\Testbench\Foundation\Env;
use Orchestra\Testbench\Foundation\Workbench;
use Symfony\Component\Finder\Finder;

use function Orchestra\Testbench\workbench_path;

class LoadConfiguration
{
    /**
     * Bootstrap the given application.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
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

        mb_internal_encoding('UTF-8');
    }

    /**
     * Load the configuration items from all of the files.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @param  \Illuminate\Contracts\Config\Repository  $config
     * @return void
     */
    protected function loadConfigurationFiles(Application $app, RepositoryContract $config): void
    {
        $workbenchConfig = (Workbench::configuration()->getWorkbenchDiscoversAttributes()['config'] ?? false) && is_dir(workbench_path('config'));

        $loadedConfigurations = LazyCollection::make(static function () use ($app) {
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
            ->transform(static function ($path, $key) use ($workbenchConfig) {
                return $workbenchConfig === true && is_file(workbench_path("config/{$key}.php"))
                    ? workbench_path("config/{$key}.php")
                    : $path;
            });

        if ($workbenchConfig === true) {
            $loadedConfigurations->merge(
                LazyCollection::make(static function () {
                    foreach (Finder::create()->files()->name('*.php')->in(workbench_path('config')) as $file) {
                        yield basename($file->getRealPath(), '.php') => $file->getRealPath();
                    }
                })->reject(function ($path, $key) use ($loadedConfigurations) {
                    return $loadedConfigurations->has($key);
                })
            );
        }

        $loadedConfigurations->each(static function ($path, $key) use ($config) {
            $config->set($key, require $path);
        });
    }
}
