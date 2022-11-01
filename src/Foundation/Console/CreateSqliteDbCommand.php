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
    protected $signature = 'package:create-sqlite-db {--force : Overwrite any existing files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create SQLite database from "database.sqlite.example"';

    /**
     * Execute the console command.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $filesystem
     * @return int
     */
    public function handle(Filesystem $filesystem)
    {
        $this->copySqliteDatabaseFile($filesystem);

        return Command::SUCCESS;
    }

    /**
     * Copy the "database.sqlite" file.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $filesystem
     * @return void
     */
    protected function copySqliteDatabaseFile(Filesystem $filesystem): void
    {
        $from = $this->laravel->databasePath('database.sqlite.example');
        $to = $this->laravel->databasePath('database.sqlite');

        if (! $filesystem->exists($from)) {
            return;
        }

        if ($this->option('force') || ! $filesystem->exists($to)) {
            $filesystem->copy($from, $to);

            $this->status($from, $to, 'file');
        } else {
            $this->components->twoColumnDetail(
                sprintf('File [%s] already exists', $to),
                '<fg=yellow;options=bold>SKIPPED</>'
            );
        }
    }
}
