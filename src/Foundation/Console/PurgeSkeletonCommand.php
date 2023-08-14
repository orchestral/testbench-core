<?php

namespace Orchestra\Testbench\Foundation\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
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
            Collection::make([
                ...$filesystem->glob($this->laravel->basePath('storage/app/public/*')),
                ...$filesystem->glob($this->laravel->basePath('storage/app/*')),
                ...$filesystem->glob($this->laravel->basePath('storage/framework/sessions/*')),
                ...$filesystem->glob($this->laravel->basePath('storage/framework/views/*.php')),
            ]),
        );

        $this->deleteFilesFrom(
            $filesystem,
            Collection::make([
                ...Collection::make(['database/database.sqlite', 'bootstrap/cache/routes-v7.php'])
                    ->map(fn ($file) => $this->laravel->basePath($file))
                    ->all(),
                ...$filesystem->glob($this->laravel->basePath('routes/testbench-*.php')),
                ...$filesystem->glob($this->laravel->basePath('storage/logs/*.log')),
            ]),
            fn ($file) => $this->components->task(
                sprintf('File [%s] has been deleted', $file)
            )
        );

        $this->deleteFilesFrom(
            $filesystem,
            Collection::make($files)
                ->map(fn ($file) => $this->laravel->basePath($file))
                ->tap(function ($collect) use ($filesystem) {
                    $collect->each(function ($file) use ($collect, $filesystem) {
                        if (str_contains($file, '*')) {
                            $collect->push(...$filesystem->glob($file));
                        }
                    });
                })->reject(fn ($file) => str_contains($file, '*')),
            fn ($file) => $this->components->task(
                sprintf('File [%s] has been deleted', $file)
            )
        );

        $this->deleteDirectoriesFrom(
            $filesystem,
            Collection::make($directories)
                ->map(fn ($directory) => $this->laravel->basePath($directory))
                ->tap(function ($collect) use ($filesystem) {
                    $collect->each(function ($directory) use ($collect, $filesystem) {
                        if (str_contains($directory, '*')) {
                            $collect->push(...$filesystem->glob($directory));
                        }
                    });
                })->reject(fn ($directory) => str_contains($directory, '*')),
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
     * @param  \Illuminate\Support\Collection  $directories
     * @param  (callable(string):(void))|null  $callback
     * @return void
     */
    protected function deleteDirectoriesFrom(Filesystem $filesystem, Collection $directories, ?callable $callback = null): void
    {
        $workingPath = $this->laravel->basePath();

        $directories->filter(fn ($directory) => $filesystem->isDirectory($directory))
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
     * @param  \Illuminate\Support\Collection  $files
     * @param  (callable(string):(void))|null  $callback
     * @return void
     */
    protected function deleteFilesFrom(Filesystem $filesystem, Collection $files, ?callable $callback = null): void
    {
        $workingPath = $this->laravel->basePath();

        $files->filter(fn ($file) => $filesystem->exists($file))
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
