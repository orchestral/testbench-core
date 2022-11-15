<?php

namespace Orchestra\Testbench\Foundation\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class DropSqliteDbCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'package:drop-sqlite-db';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Drop sqlite database file';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $filesystem = new Filesystem();

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
