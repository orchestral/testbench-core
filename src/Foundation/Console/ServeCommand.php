<?php

namespace Orchestra\Testbench\Foundation\Console;

use Illuminate\Filesystem\Filesystem;

class ServeCommand extends \Illuminate\Foundation\Console\ServeCommand
{
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

        /** @phpstan-ignore-next-line */
        $workingPath = TESTBENCH_WORKING_PATH;

        $this->copyTestbenchConfigurationFile($filesystem, $workingPath);
        $this->copyTestbenchDotEnvFile($filesystem, $workingPath);

        return parent::handle();
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
        $configurationFile = collect([
            'testbench.yaml',
            'testbench.yaml.example',
            'testbench.yaml.dist',
        ])->map(fn ($file) => "{$workingPath}/{$file}")
        ->filter(fn ($file) => $filesystem->exists($file))
        ->first();

        if (! is_null($configurationFile)) {
            $filesystem->copy($configurationFile, $this->laravel->basePath('testbench.yaml'));
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
        $configurationFile = collect([
            '.env',
            '.env.example',
            '.env.dist',
        ])->map(fn ($file) => "{$workingPath}/{$file}")
        ->filter(fn ($file) => $filesystem->exists($file))
        ->first();

        if (! is_null($configurationFile)) {
            $filesystem->copy($configurationFile, $this->laravel->basePath('.env'));
        }
    }
}
