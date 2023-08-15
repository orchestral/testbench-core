<?php

namespace Orchestra\Testbench\Foundation\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'package:drop-sqlite-db', description: 'Drop sqlite database file')]
class DropSqliteDbCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'package:drop-sqlite-db';

    /**
     * Execute the console command.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $filesystem
     * @return int
     */
    public function handle(Filesystem $filesystem)
    {
        $workingPath = $this->laravel->basePath();
        $databasePath = $this->laravel->databasePath();

        $from = realpath(__DIR__.'/stubs/database.sqlite.example');
        $to = "{$databasePath}/database.sqlite";

        if (! $filesystem->exists($to)) {
            $this->components->twoColumnDetail(
                sprintf('File [%s] does not exists', str_replace($workingPath.'/', '', $to)),
                '<fg=yellow;options=bold>SKIPPED</>'
            );
        } else {
            $filesystem->delete($to);

            $this->components->task(
                sprintf('File [%s] has been deleted', str_replace($workingPath.'/', '', $to))
            );
        }

        return Command::SUCCESS;
    }
}
