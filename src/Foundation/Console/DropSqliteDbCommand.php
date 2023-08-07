<?php

namespace Orchestra\Testbench\Foundation\Console;

use Illuminate\Console\Command;

/**
 * @deprecated
 */
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
    protected $description = 'Drop sqlite database file (deprecated)';

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
        $this->call('workbench:drop-sqlite-db');

        return Command::SUCCESS;
    }
}
