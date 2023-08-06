<?php

namespace Orchestra\Testbench\Workbench\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Orchestra\Testbench\Foundation\Console\Concerns\InteractsWithIO;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'workbench:create-sqlite-db')]
class CreateSqliteDbCommand extends Command
{
    use InteractsWithIO;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'workbench:create-sqlite-db {--force : Overwrite the database file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create sqlite database file';

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
