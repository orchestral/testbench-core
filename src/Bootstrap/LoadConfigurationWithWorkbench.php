<?php

namespace Orchestra\Testbench\Bootstrap;

use Illuminate\Support\Enumerable;
use Illuminate\Support\LazyCollection;
use Symfony\Component\Finder\Finder;

use function Orchestra\Testbench\workbench_path;

/**
 * @internal
 */
class LoadConfigurationWithWorkbench extends LoadConfiguration
{
    /**
     * Resolve the configuration file.
     *
     * @param  string  $path
     * @param  string  $key
     * @return string
     */
    protected function resolveConfigurationFile(string $path, string $key): string
    {
        return is_file(workbench_path("config/{$key}.php"))
            ? workbench_path("config/{$key}.php")
            : $path;
    }

    /**
     * Extend the loaded configuration.
     *
     * @param  \Illuminate\Support\Enumerable  $configurations
     * @return void
     */
    protected function extendsLoadedConfiguration(Enumerable $configurations): void
    {
        $workbenchConfigurations = LazyCollection::make(static function () {
            if (is_dir(workbench_path('config'))) {
                foreach (Finder::create()->files()->name('*.php')->in(workbench_path('config')) as $file) {
                    yield basename($file->getRealPath(), '.php') => $file->getRealPath();
                }
            }
        })->reject(static function ($path, $key) use ($configurations) {
            return $configurations->has($key);
        })->each(static function ($path, $key) use ($configurations) {
            $configurations->put($key, $path);
        });
    }
}
