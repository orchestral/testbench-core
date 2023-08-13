<?php

namespace Orchestra\Testbench\Foundation\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'package:clear-skeleton', description: 'Clear skeleton folder to original state')]
class ClearSkeletonCommand extends Command
{
    use Concerns\InteractsWithIO;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'package:clear-skeleton';

    /**
     * Execute the console command.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $filesystem
     * @return int
     */
    public function handle(Filesystem $filesystem)
    {
        $this->deleteFilesFromCollection(
            $filesystem,
            Collection::make([
                '.env',
                'testbench.yaml',
            ])->map(fn ($file) => $this->laravel->basePath($file)),
            false
        );

        $this->deleteFilesFromCollection(
            $filesystem,
            Collection::make([
                'database/database.sqlite',
                'bootstrap/cache/routes-v7.php',
            ])->map(fn ($file) => $this->laravel->basePath($file))
                ->merge([
                    ...$filesystem->glob($this->laravel->basePath('routes/testbench-*.php')),
                    ...$filesystem->glob($this->laravel->basePath('storage/logs/*.log')),
                ])
        );

        return Command::SUCCESS;
    }

    protected function deleteFilesFromCollection(Filesystem $filesystem, Collection $files, bool $output = true): void
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
