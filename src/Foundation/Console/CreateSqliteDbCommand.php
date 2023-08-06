<?php

namespace Orchestra\Testbench\Foundation\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

/**
 * @deprecated
 */
#[AsCommand(name: 'package:create-sqlite-db')]
class CreateSqliteDbCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'package:create-sqlite-db {--force : Overwrite the database file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create sqlite database file (deprecated)';

    /**
     * Indicates whether the command should be shown in the Artisan command list.
     *
     * @var bool
     */
    protected $hidden = true;

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->call('workbench:create-sqlite-db', ['--force' => $this->option('force')]);

        return Command::SUCCESS;
    }
}
