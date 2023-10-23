<?php

namespace Orchestra\Testbench\Concerns;

use Illuminate\Support\Collection;

trait InteractsWithPublishedFiles
{
    /**
     * Determine if trait teardown has been registered.
     *
     * @var bool
     */
    protected $interactsWithPublishedFilesTeardownRegistered = false;

    /**
     * Setup Interacts with Published Files environment.
     */
    protected function setUpInteractsWithPublishedFiles(): void
    {
        $this->cleanUpPublishedFiles();
        $this->cleanUpPublishedMigrationFiles();

        $this->beforeApplicationDestroyed(function () {
            $this->tearDownInteractsWithPublishedFiles();
        });
    }

    /**
     * Teardown Interacts with Published Files environment.
     */
    protected function tearDownInteractsWithPublishedFiles(): void
    {
        if ($this->interactsWithPublishedFilesTeardownRegistered === false) {
            $this->cleanUpPublishedFiles();
            $this->cleanUpPublishedMigrationFiles();
        }

        $this->interactsWithPublishedFilesTeardownRegistered = true;
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
    protected function assertMigrationFileContains(array $contains, string $file, string $message = '', ?string $directory = null): void
    {
        $migrationFile = $this->findFirstPublishedMigrationFile($file, $directory);

        $this->assertTrue(! \is_null($migrationFile), "Assert migration file {$file} does exist");

        $haystack = $this->app['files']->get($migrationFile);

        foreach ($contains as $needle) {
            $this->assertStringContainsString($needle, $haystack, $message);
        }
    }

    /**
     * Assert file doesn't contains data.
     *
     * @param  array<int, string>  $contains
     */
    protected function assertMigrationFileDoesNotContains(array $contains, string $file, string $message = '', ?string $directory = null): void
    {
        $migrationFile = $this->findFirstPublishedMigrationFile($file, $directory);

        $this->assertTrue(! \is_null($migrationFile), "Assert migration file {$file} does exist");

        $haystack = $this->app['files']->get($migrationFile);

        foreach ($contains as $needle) {
            $this->assertStringNotContainsString($needle, $haystack, $message);
        }
    }

    /**
     * Assert file doesn't contains data.
     *
     * @param  array<int, string>  $contains
     */
    protected function assertMigrationFileNotContains(array $contains, string $file, string $message = '', ?string $directory = null): void
    {
        $this->assertMigrationFileDoesNotContains($contains, $file, $message, $directory);
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
     * Assert migration filename exists.
     */
    protected function assertMigrationFileExists(string $file, ?string $directory = null): void
    {
        $migrationFile = $this->findFirstPublishedMigrationFile($file, $directory);

        $this->assertTrue(! \is_null($migrationFile), "Assert migration file {$file} does exist");
    }

    /**
     * Assert migration filename not exists.
     */
    protected function assertMigrationFileDoesNotExists(string $file, ?string $directory = null): void
    {
        $migrationFile = $this->findFirstPublishedMigrationFile($file, $directory);

        $this->assertTrue(\is_null($migrationFile), "Assert migration file {$file} doesn't exist");
    }

    /**
     * Assert migration filename not exists.
     */
    protected function assertMigrationFileNotExists(string $file, ?string $directory = null): void
    {
        $this->assertMigrationFileNotExists($file, $directory);
    }

    /**
     * Removes generated files.
     */
    protected function cleanUpPublishedFiles(): void
    {
        $this->app['files']->delete(
            Collection::make($this->files ?? [])
                ->transform(fn ($file) => $this->app->basePath($file))
                ->map(fn ($file) => str_contains($file, '*') ? [...$this->app['files']->glob($file)] : $file)
                ->flatten()
                ->filter(fn ($file) => $this->app['files']->exists($file))
                ->reject(static function ($file) {
                    return str_ends_with($file, '.gitkeep') || str_ends_with($file, '.gitignore');
                })->all()
        );
    }

    /**
     * Removes generated migration files.
     */
    protected function findFirstPublishedMigrationFile(string $filename, ?string $directory = null): ?string
    {
        $migrationPath = ! \is_null($directory)
            ? $this->app->basePath($directory)
            : $this->app->databasePath('migrations');

        return $this->app['files']->glob("{$migrationPath}/*{$filename}")[0] ?? null;
    }

    /**
     * Removes generated migration files.
     */
    protected function cleanUpPublishedMigrationFiles(): void
    {
        $this->app['files']->delete(
            Collection::make($this->app['files']->files($this->app->databasePath('migrations')))
                ->filter(static function ($file) {
                    return str_ends_with($file, '.php');
                })->all()
        );
    }
}
