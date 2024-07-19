<?php

namespace Orchestra\Testbench\Concerns;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * @internal
 */
trait InteractsWithPublishedFiles
{
    /**
     * Determine if trait teardown has been registered.
     *
     * @var bool
     */
    protected $interactsWithPublishedFilesTeardownRegistered = false;

    /**
     * List of existing migration files.
     *
     * @var array
     */
    protected $cachedExistingMigrationsFiles = [];

    /**
     * Setup Interacts with Published Files environment.
     */
    protected function setUpInteractsWithPublishedFiles(): void
    {
        $this->cacheExistingMigrationsFiles();

        $this->cleanUpFiles();
        $this->cleanUpMigrationFiles();
    }

    /**
     * Teardown Interacts with Published Files environment.
     */
    protected function tearDownInteractsWithPublishedFiles(): void
    {
        if ($this->interactsWithPublishedFilesTeardownRegistered === false) {
            $this->cleanUpFiles();
            $this->cleanUpMigrationFiles();
        }

        $this->cachedExistingMigrationsFiles = [];
        $this->interactsWithPublishedFilesTeardownRegistered = true;
    }

    /**
     * Cache existing migration files.
     *
     * @internal
     *
     * @return void
     */
    protected function cacheExistingMigrationsFiles()
    {
        $this->cachedExistingMigrationsFiles = Collection::make($this->app['files']->files($this->app->databasePath('migrations')))
            ->filter(static function ($file) {
                return Str::endsWith($file, '.php');
            })->all();
    }

    /**
     * Assert file does contains data.
     *
     * @param  array<int, string>  $contains
     */
    protected function assertFileContains(array $contains, string $file, string $message = ''): void
    {
        $this->assertFilenameExists($file);

        $haystack = $this->app['files']->get(
            $this->app->basePath($file)
        );

        foreach ($contains as $needle) {
            $this->assertStringContainsString($needle, $haystack, $message);
        }
    }

    /**
     * Assert file doesn't contains data.
     *
     * @param  array<int, string>  $contains
     */
    protected function assertFileDoesNotContains(array $contains, string $file, string $message = ''): void
    {
        $this->assertFilenameExists($file);

        $haystack = $this->app['files']->get(
            $this->app->basePath($file)
        );

        foreach ($contains as $needle) {
            $this->assertStringNotContainsString($needle, $haystack, $message);
        }
    }

    /**
     * Assert file doesn't contains data.
     *
     * @param  array<int, string>  $contains
     */
    protected function assertFileNotContains(array $contains, string $file, string $message = ''): void
    {
        $this->assertFileDoesNotContains($contains, $file, $message);
    }

    /**
     * Assert file does contains data.
     *
     * @param  array<int, string>  $contains
     */
    protected function assertMigrationFileContains(array $contains, string $file, string $message = ''): void
    {
        $haystack = $this->app['files']->get($this->getMigrationFile($file));

        foreach ($contains as $needle) {
            $this->assertStringContainsString($needle, $haystack, $message);
        }
    }

    /**
     * Assert file doesn't contains data.
     *
     * @param  array<int, string>  $contains
     */
    protected function assertMigrationFileDoesNotContains(array $contains, string $file, string $message = ''): void
    {
        $haystack = $this->app['files']->get($this->getMigrationFile($file));

        foreach ($contains as $needle) {
            $this->assertStringNotContainsString($needle, $haystack, $message);
        }
    }

    /**
     * Assert file doesn't contains data.
     *
     * @param  array<int, string>  $contains
     */
    protected function assertMigrationFileNotContains(array $contains, string $file, string $message = ''): void
    {
        $this->assertMigrationFileDoesNotContains($contains, $file, $message);
    }

    /**
     * Assert filename exists.
     */
    protected function assertFilenameExists(string $file): void
    {
        $appFile = $this->app->basePath($file);

        $this->assertTrue($this->app['files']->exists($appFile), "Assert file {$file} does exist");
    }

    /**
     * Assert filename not exists.
     */
    protected function assertFilenameDoesNotExists(string $file): void
    {
        $appFile = $this->app->basePath($file);

        $this->assertTrue(! $this->app['files']->exists($appFile), "Assert file {$file} doesn't exist");
    }

    /**
     * Assert filename not exists.
     */
    protected function assertFilenameNotExists(string $file): void
    {
        $this->assertFilenameDoesNotExists($file);
    }

    /**
     * Removes generated files.
     */
    protected function cleanUpFiles(): void
    {
        $this->app['files']->delete(
            Collection::make($this->files ?? [])
                ->transform(function ($file) {
                    return $this->app->basePath($file);
                })
                ->filter(function ($file) {
                    return $this->app['files']->exists($file);
                })->all()
        );
    }

    /**
     * Removes generated migration files.
     */
    protected function getMigrationFile(string $filename): string
    {
        $migrationPath = $this->app->databasePath('migrations');

        return $this->app['files']->glob("{$migrationPath}/*{$filename}")[0];
    }

    /**
     * Removes generated migration files.
     */
    protected function cleanUpMigrationFiles(): void
    {
        $this->app['files']->delete(
            Collection::make($this->app['files']->files($this->app->databasePath('migrations')))
                ->reject(function ($file) {
                    return \in_array($file, $this->cachedExistingMigrationsFiles);
                })->filter(static function ($file) {
                    return Str::endsWith($file, '.php');
                })->all()
        );
    }
}
