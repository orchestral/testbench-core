<?php

namespace Orchestra\Testbench\Workbench\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'workbench:drop-sqlite-db')]
class DropSqliteDbCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'workbench:drop-sqlite-db';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Drop sqlite database file';

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

            $this->components->twoColumnDetail(
                sprintf('File [%s] has been deleted', str_replace($workingPath.'/', '', $to)),
                '<fg=green;options=bold>DONE</>'
            );
        }

        return Command::SUCCESS;
    }
}
