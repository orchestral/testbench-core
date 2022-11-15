<?php

namespace Orchestra\Testbench\Foundation\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class CreateSqliteDbCommand extends Command
{
    use Concerns\InteractsWithIO;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'package:create-sqlite-db  {--force : Overwrite the database file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create sqlite database file';

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

        $from = $filesystem->exists("{$databasePath}/database.sqlite.example")
            ? "{$databasePath}/database.sqlite.example"
            : (string) realpath(__DIR__.'/stubs/database.sqlite.example');
        $to = "{$databasePath}/database.sqlite";

        if ($this->option('force') || ! $filesystem->exists($to)) {
            $filesystem->copy($from, $to);

            $this->status($from, $to, 'file', $workingPath);
        } else {
            $this->components->twoColumnDetail(
                sprintf('File [%s] already exists', str_replace($workingPath.'/', '', $to)),
                '<fg=yellow;options=bold>SKIPPED</>'
            );
        }

        return Command::SUCCESS;
    }
}
