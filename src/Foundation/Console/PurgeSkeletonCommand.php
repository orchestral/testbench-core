<?php

namespace Orchestra\Testbench\Foundation\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
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
    public function handle(Filesystem $filesystem)
    {

        $this->deleteFilesFrom(
            $filesystem,
            Collection::make([
                '.env',
                'testbench.yaml',
            ])->map(fn ($file) => $this->laravel->basePath($file)),
            false
        );

        $this->deleteFilesFrom(
            $filesystem,
            Collection::make([
                ...collect(['database/database.sqlite', 'bootstrap/cache/routes-v7.php'])
                    ->map(fn ($file) => $this->laravel->basePath($file))
                    ->all(),
                ...$filesystem->glob($this->laravel->basePath('routes/testbench-*.php')),
                ...$filesystem->glob($this->laravel->basePath('storage/logs/*.log')),
            ])
        );

        $this->deleteDirectoriesFrom(
            $filesystem,
            Collection::make([
            ])
        );

        return Command::SUCCESS;
    }

    /**
     * Delete set of file from collection.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $filesystem
     * @param  \Illuminate\Support\Collection  $directories
     * @param  bool  $output
     * @return void
     */
    protected function deleteDirectoriesFrom(Filesystem $filesystem, Collection $directories, bool $output = true): void
    {
        $workingPath = $this->laravel->basePath();

        $directories->filter(fn ($directory) => $filesystem->isDirectory($directory))
            ->each(function ($directory) use ($filesystem, $workingPath, $output) {
                $filesystem->deleteDirectory($directory);

                if ($output === true) {
                    $this->components->task(
                        sprintf('Directory [%s] has been deleted', str_replace($workingPath.'/', '', $directory))
                    );
                }
            });
    }

    /**
     * Delete set of file from collection.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $filesystem
     * @param  \Illuminate\Support\Collection  $files
     * @param  bool  $output
     * @return void
     */
    protected function deleteFilesFrom(Filesystem $filesystem, Collection $files, bool $output = true): void
    {
        $workingPath = $this->laravel->basePath();

        $files->filter(fn ($file) => $filesystem->exists($file))
            ->each(function ($file) use ($filesystem, $workingPath, $output) {
                $filesystem->delete($file);

                if ($output === true) {
                    $this->components->task(
                        sprintf('File [%s] has been deleted', str_replace($workingPath.'/', '', $file))
                    );
                }
            });
    }
}
