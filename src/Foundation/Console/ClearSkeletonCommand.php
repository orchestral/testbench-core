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
        $workingPath = $this->laravel->basePath();

        Collection::make([
            '.env',
            'testbench.yaml',
            'database/database.sqlite',
            'bootstrap/cache/routes-v7.php',
        ])->map(fn ($file) => $this->laravel->basePath($file))
            ->merge([
                ...$filesystem->glob($this->laravel->basePath('routes/testbench-*.php'))
            ])->filter(fn ($file) => $filesystem->exists($file))
            ->each(function ($file) use ($filesystem, $workingPath) {
                $filesystem->delete($file);

                $this->components->twoColumnDetail(
                    sprintf('File [%s] has been deleted', str_replace($workingPath.'/', '', $file)),
                    '<fg=green;options=bold>DONE</>'
                );
            });

        return Command::SUCCESS;
    }
}
