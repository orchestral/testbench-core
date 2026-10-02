<?php

namespace Orchestra\Testbench\Foundation\Console\Concerns;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\ParallelTesting;
use Illuminate\Support\LazyCollection;
use Orchestra\Sidekick\Env;
use Orchestra\Testbench\Foundation\Console\TerminatingConsole;

use function Orchestra\Sidekick\Filesystem\join_paths;
use function Orchestra\Testbench\in_parallel_testing;

/**
 * @codeCoverageIgnore
 */
trait CopyTestbenchFiles
{
    /**
     * Copy the "testbench.yaml" file.
     *
     * @internal
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
        bool $backupExistingFile = true,
        bool $resetOnTerminating = true
    ): void {
        $configurationFilePath = (new LazyCollection(static function () {
            yield 'testbench.yaml';
            yield 'testbench.yaml.example';
            yield 'testbench.yaml.dist';
        }))->map(static fn ($file) => join_paths($workingPath, $file))
            ->filter(static fn ($file) => $filesystem->isFile($file))
            ->first();

        $testbenchFilePath = $app->basePath(join_paths('bootstrap', 'cache', 'testbench.yaml'));

        if ($backupExistingFile === true && $filesystem->isFile($testbenchFilePath)) {
            $filesystem->copy($testbenchFilePath, "{$testbenchFilePath}.backup");

            TerminatingConsole::beforeWhen($resetOnTerminating, static function () use ($filesystem, $testbenchFilePath) {
                if ($filesystem->isFile("{$testbenchFilePath}.backup")) {
                    $filesystem->move("{$testbenchFilePath}.backup", $testbenchFilePath);
                }
            });
        }

        if (! \is_null($configurationFilePath)) {
            $filesystem->copy($configurationFilePath, $testbenchFilePath);

            TerminatingConsole::beforeWhen($resetOnTerminating, static function () use ($filesystem, $testbenchFilePath) {
                if ($filesystem->isFile($testbenchFilePath)) {
                    $filesystem->delete($testbenchFilePath);
                }
            });
        }
    }

    /**
     * Copy the ".env" file.
     *
     * @internal
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
        bool $backupExistingFile = true,
        bool $resetOnTerminating = true
    ): void {
        $workingPath = $filesystem->isDirectory(join_paths($workingPath, 'workbench'))
            ? join_paths($workingPath, 'workbench')
            : $workingPath;

        $testbenchEnvFile = $this->testbenchEnvironmentFile();

        $configurationFilePath = (new LazyCollection(static function () use ($testbenchEnvFile) {
            $defaultTestbenchEnvFile = '.env';

            yield $testbenchEnvFile;
            yield "{$testbenchEnvFile}.example";
            yield "{$testbenchEnvFile}.dist";

            yield $defaultTestbenchEnvFile;
            yield "{$defaultTestbenchEnvFile}.example";
            yield "{$defaultTestbenchEnvFile}.dist";
        }))->unique()
            ->map(static fn ($file) => join_paths($workingPath, $file))
            ->filter(static fn ($file) => $filesystem->isFile($file))
            ->first();

        if (\is_null($configurationFilePath) && $filesystem->isFile($app->basePath('.env.example'))) {
            $configurationFilePath = $app->basePath('.env.example');
        }

        $environmentFile = '.env';

        if (in_parallel_testing()) {
            $environmentFile = \sprintf('.env_test_%d', ParallelTesting::token());
            $backupExistingFile = false;
        }

        $environmentFilePath = $app->basePath($environmentFile);

        if ($backupExistingFile === true && $filesystem->isFile($environmentFilePath)) {
            $filesystem->copy($environmentFilePath, "{$environmentFilePath}.backup");

            TerminatingConsole::beforeWhen($resetOnTerminating, static function () use ($filesystem, $environmentFilePath) {
                $filesystem->move("{$environmentFilePath}.backup", $environmentFilePath);
            });
        }

        if (! \is_null($configurationFilePath)) {
            $filesystem->copy($configurationFilePath, $environmentFilePath);

            TerminatingConsole::beforeWhen($resetOnTerminating, static function () use ($filesystem, $environmentFilePath) {
                $filesystem->delete($environmentFilePath);
            });
        }
    }

    /**
     * Determine the Testbench's environment file.
     *
     * @internal
     *
     * @return string
     */
    protected function testbenchEnvironmentFile(): string
    {
        return match (true) {
            property_exists($this, 'environmentFile') => $this->environmentFile,
            Env::has('TESTBENCH_ENVIRONMENT_FILENAME') => Env::get('TESTBENCH_ENVIRONMENT_FILENAME'),
            default => '.env',
        };
    }
}
