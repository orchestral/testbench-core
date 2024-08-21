<?php

namespace Orchestra\Testbench\Bootstrap;

use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use Orchestra\Testbench\Workbench\Workbench;
use Symfony\Component\Finder\Finder;

use function Orchestra\Testbench\workbench_path;

/**
 * @internal
 */
class LoadConfigurationWithWorkbench extends LoadConfiguration
{
    /**
     * Determine if workbench config file should be loaded.
     *
     * @var bool
     */
    protected $usesWorkbenchConfigFile = false;

    /**
     * Construct a new bootstrap class.
     */
    public function __construct()
    {
        $this->usesWorkbenchConfigFile = (Workbench::configuration()->getWorkbenchDiscoversAttributes()['config'] ?? false)
            && is_dir(workbench_path('config'));
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
        return $this->usesWorkbenchConfigFile === true && is_file(workbench_path('config', "{$key}.php"))
            ? workbench_path('config', "{$key}.php")
            : $path;
    }

    /**
     * Extend the loaded configuration.
     *
     * @param  \Illuminate\Support\Collection  $configurations
     * @return \Illuminate\Support\Collection
     */
    protected function extendsLoadedConfiguration(Collection $configurations): Collection
    {
        if ($this->usesWorkbenchConfigFile === false) {
            return $configurations;
        }

        LazyCollection::make(function () {
            $path = workbench_path('config');

            foreach (Finder::create()->files()->name('*.php')->in($path) as $file) {
                $directory = $this->getNestedDirectory($file, $path);

                yield $directory.basename($file->getRealPath(), '.php') => $file->getRealPath();
            }
        })->reject(static function ($path, $key) use ($configurations) {
            return $configurations->has($key);
        })->each(static function ($path, $key) use ($configurations) {
            $configurations->put($key, $path);
        });

        return $configurations;
    }
}
