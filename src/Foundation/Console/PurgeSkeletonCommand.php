<?php

namespace Orchestra\Testbench\Foundation\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Enumerable;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Str;
use Orchestra\Testbench\Contracts\Config as ConfigContract;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'package:purge-skeleton', description: 'Purge skeleton folder to original state')]
class PurgeSkeletonCommand extends Command
{
    use Concerns\InteractsWithIO;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'package:purge-skeleton';

    /**
     * Execute the console command.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $filesystem
     * @return int
     */
    public function handle(Filesystem $filesystem, ConfigContract $config)
    {
        $this->call('config:clear');
        $this->call('event:clear');
        $this->call('route:clear');
        $this->call('view:clear');

        ['files' => $files, 'directories' => $directories] = $config->getPurgeAttributes();

        $this->deleteFilesFrom(
            $filesystem,
            Collection::make([
                '.env',
                'testbench.yaml',
            ])->map(fn ($file) => $this->laravel->basePath($file)),
        );

        $this->deleteFilesFrom(
            $filesystem,
            LazyCollection::make(function () use ($filesystem) {
                yield $this->laravel->basePath('database/database.sqlite');
                yield $filesystem->glob($this->laravel->basePath('routes/testbench-*.php'));
                yield $filesystem->glob($this->laravel->basePath('storage/app/public/*'));
                yield $filesystem->glob($this->laravel->basePath('storage/app/*'));
                yield $filesystem->glob($this->laravel->basePath('storage/framework/sessions/*'));
            })->flatten(),
        );

        $this->deleteFilesFrom(
            $filesystem,
            LazyCollection::make($files)
                ->map(fn ($file) => $this->laravel->basePath($file))
                ->map(function ($file) use ($filesystem) {
                    return str_contains($file, '*')
                        ? [...$filesystem->glob($file)]
                        : $file;
                })->flatten()
                ->reject(fn ($file) => str_contains($file, '*')),
            fn ($file) => $this->components->task(
                sprintf('File [%s] has been deleted', $file)
            )
        );

        $this->deleteDirectoriesFrom(
            $filesystem,
            Collection::make($directories)
                ->map(fn ($directory) => $this->laravel->basePath($directory))
                ->map(function ($directory) use ($filesystem) {
                    return str_contains($directory, '*')
                        ? [...$filesystem->glob($directory)]
                        : $directory;
                })->flatten()
                ->reject(fn ($directory) => str_contains($directory, '*')),
            fn ($directory) => $this->components->task(
                sprintf('Directory [%s] has been deleted', $directory)
            )
        );

        return Command::SUCCESS;
    }

    /**
     * Delete set of file from collection.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $filesystem
     * @param  \Illuminate\Support\Enumerable  $directories
     * @param  (callable(string):(void))|null  $callback
     * @return void
     */
    protected function deleteDirectoriesFrom(Filesystem $filesystem, Enumerable $directories, ?callable $callback = null): void
    {
        $workingPath = $this->laravel->basePath();

        LazyCollection::make($directories)
            ->filter(fn ($directory) => $filesystem->isDirectory($directory))
            ->each(function ($directory) use ($filesystem, $workingPath, $callback) {
                $filesystem->deleteDirectory($directory);

                $directoryName = str_replace($workingPath.'/', '', $directory);

                if (\is_callable($callback)) {
                    \call_user_func($callback, $directoryName);
                }
            });
    }

    /**
     * Delete set of file from collection.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $filesystem
     * @param  \Illuminate\Support\Enumerable  $files
     * @param  (callable(string):(void))|null  $callback
     * @return void
     */
    protected function deleteFilesFrom(Filesystem $filesystem, Enumerable $files, ?callable $callback = null): void
    {
        $workingPath = $this->laravel->basePath();

        LazyCollection::make($files)
            ->filter(fn ($file) => $filesystem->exists($file))
            ->reject(fn ($file) => Str::endsWith($file, ['.gitkeep', '.gitignore']))
            ->each(function ($file) use ($filesystem, $workingPath, $callback) {
                $filesystem->delete($file);

                $fileName = str_replace($workingPath.'/', '', $file);

                if (\is_callable($callback)) {
                    \call_user_func($callback, $fileName);
                }
            });
    }
}
