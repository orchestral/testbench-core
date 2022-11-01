<?php

namespace Orchestra\Testbench\Foundation\Console;

use Illuminate\Console\Signals;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Console\ServeCommand as Command;
use Illuminate\Support\Collection;

class ServeCommand extends Command
{
    /**
     * The environment file name.
     *
     * @var string
     */
    protected $environmentFile = '.env';

    /**
     * The terminating callbacks.
     *
     * @var array<int, (callable(\Illuminate\Filesystem\Filesystem):void)>
     */
    protected $terminatingCallbacks = [];

    /**
     * Execute the console command.
     *
     * @return int
     *
     * @throws \Exception
     */
    public function handle()
    {
        $filesystem = new Filesystem();

        $this->prepareCommandSignals($filesystem);

        /** @phpstan-ignore-next-line */
        $workingPath = TESTBENCH_WORKING_PATH;

        $this->copyTestbenchConfigurationFile($filesystem, $workingPath);
        $this->copyTestbenchDotEnvFile($filesystem, $workingPath);

        return parent::handle();
    }

    /**
     * Prepare command signals.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $filesystem
     * @return void
     */
    protected function prepareCommandSignals(Filesystem $filesystem): void
    {
        Signals::resolveAvailabilityUsing(function () {
            return extension_loaded('pcntl');
        });

        $this->trap([SIGINT], function ($signal) use ($filesystem) {
            collect($this->terminatingCallbacks)
                ->each(function ($callback) use ($filesystem) {
                    call_user_func($callback, $filesystem);
                });
        });
    }

    /**
     * Copy the "testbench.yaml" file.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $filesystem
     * @param  string  $workingPath
     * @return void
     */
    protected function copyTestbenchConfigurationFile(Filesystem $filesystem, string $workingPath): void
    {
        $configurationFile = Collection::make([
            'testbench.yaml',
            'testbench.yaml.example',
            'testbench.yaml.dist',
        ])->map(fn ($file) => "{$workingPath}/{$file}")
        ->filter(fn ($file) => $filesystem->exists($file))
        ->first();

        $testbenchFile = $this->laravel->basePath('testbench.yaml');

        if (! is_null($configurationFile)) {
            $filesystem->copy($configurationFile, $testbenchFile);

            $this->terminatingCallbacks[] = function (Filesystem $filesystem) use ($testbenchFile) {
                if ($filesystem->exists($testbenchFile)) {
                    $filesystem->delete($testbenchFile);
                }
            };
        }
    }

    /**
     * Copy the ".env" file.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $filesystem
     * @param  string  $workingPath
     * @return void
     */
    protected function copyTestbenchDotEnvFile(Filesystem $filesystem, string $workingPath): void
    {
        $configurationFile = Collection::make([
            $this->environmentFile,
            "{$this->environmentFile}.example",
            "{$this->environmentFile}.dist",
        ])->map(fn ($file) => "{$workingPath}/{$file}")
        ->filter(fn ($file) => $filesystem->exists($file))
        ->first();

        $environmentFile = $this->laravel->basePath('.env');

        if (! is_null($configurationFile) && ! $filesystem->exists($environmentFile)) {
            $filesystem->copy($configurationFile, $environmentFile);

            $this->terminatingCallbacks[] = function (Filesystem $filesystem) use ($environmentFile) {
                if ($filesystem->exists($environmentFile)) {
                    $filesystem->delete($environmentFile);
                }
            };
        }
    }

    /**
     * Get the value of a command option.
     *
     * @param  string|null  $key
     * @return string|array|bool|null
     */
    public function option($key = null)
    {
        if ($key === 'no-reload') {
            return true;
        }

        return parent::option($key);
    }
}
