<?php

namespace Orchestra\Testbench\Foundation\Console\Concerns;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\LazyCollection;
use Orchestra\Testbench\Foundation\Env;

use function Orchestra\Testbench\join_paths;
use Illuminate\Contracts\Foundation\Application;
use InvalidArgumentException;

trait CopyTestbenchFiles
{
    use HandleTerminatingConsole;

    /**
     * Copy the "testbench.yaml" file.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @param  \Illuminate\Filesystem\Filesystem  $filesystem
     * @param  string  $workingPath
     * @param  bool  $resetOnTerminating
     * @return void
     */
    protected function copyTestbenchConfigurationFile(
        Application $app, 
        Filesystem $filesystem, 
        string $workingPath,
        bool $resetOnTerminating = true
    ): void {
        $configurationFile = LazyCollection::make(static function () {
            yield 'testbench.yaml';
            yield 'testbench.yaml.example';
            yield 'testbench.yaml.dist';
        })->map(static fn ($file) => join_paths($workingPath, $file))
            ->filter(static fn ($file) => $filesystem->isFile($file))
            ->first();

        $testbenchFile = $app->basePath(join_paths('bootstrap', 'cache', 'testbench.yaml'));

        if ($filesystem->isFile($testbenchFile)) {
            $filesystem->copy($testbenchFile, "{$testbenchFile}.backup");

            $this->beforeTerminatingWhen($resetOnTerminating, static function () use ($filesystem, $testbenchFile) {
                if ($filesystem->isFile("{$testbenchFile}.backup")) {
                    $filesystem->move("{$testbenchFile}.backup", $testbenchFile);
                }
            });
        }

        if (! \is_null($configurationFile)) {
            $filesystem->copy($configurationFile, $testbenchFile);

            $this->beforeTerminatingWhen($resetOnTerminating, static function () use ($filesystem, $testbenchFile) {
                if ($filesystem->isFile($testbenchFile)) {
                    $filesystem->delete($testbenchFile);
                }
            });
        }
    }

    /**
     * Copy the ".env" file.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @param  \Illuminate\Filesystem\Filesystem  $filesystem
     * @param  string  $workingPath
     * @param  bool  $resetOnTerminating
     * @return void
     */
    protected function copyTestbenchDotEnvFile(
        Application $app, 
        Filesystem $filesystem, 
        string $workingPath,
        bool $resetOnTerminating = true
    ): void {
        $workingPath = $filesystem->isDirectory(join_paths($workingPath, 'workbench'))
            ? join_paths($workingPath, 'workbench')
            : $workingPath;

        $environmentFile = $this->testbenchEnvironmentFile();
            
        $configurationFile = LazyCollection::make(static function () use ($environmentFile) {
            yield $environmentFile;
            yield "{$environmentFile}.example";
            yield "{$environmentFile}.dist";
        })->map(static fn ($file) => join_paths($workingPath, $file))
            ->filter(static fn ($file) => $filesystem->isFile($file))
            ->first();

        if (\is_null($configurationFile) && $filesystem->isFile($app->basePath('.env.example'))) {
            $configurationFile = $app->basePath('.env.example');
        }

        $environmentFile = $app->basePath('.env');
        $environmentFileBackup = $app->basePath("{$this->environmentFile}.backup");

        if ($filesystem->isFile($environmentFile)) {
            $filesystem->copy($environmentFile, $environmentFileBackup);

            $this->beforeTerminatingWhen($resetOnTerminating, static function () use ($filesystem, $environmentFile, $environmentFileBackup) {
                $filesystem->move($environmentFileBackup, $environmentFile);
            });
        }

        if (! \is_null($configurationFile) && ! $filesystem->isFile($environmentFile)) {
            $filesystem->copy($configurationFile, $environmentFile);

            $this->beforeTerminatingWhen($resetOnTerminating, static function () use ($filesystem, $environmentFile) {
                $filesystem->delete($environmentFile);
            });
        }
    }

    /**
     * Determine the Testbench's environment file.
     * 
     * @return string
     */
    protected function testbenchEnvironmentFile(): string 
    {
        if (property_exists($this, 'environmentFile')) {
            return $this->environmentFile;
        } elseif (Env::has('TESTBENCH_ENVIRONMENT_FILE_USING')) {
            return Env::get('TESTBENCH_ENVIRONMENT_FILE_USING');
        }

        return '.env';
    }
}
