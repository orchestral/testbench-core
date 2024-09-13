<?php

namespace Orchestra\Testbench\Console;

use Orchestra\Testbench\Foundation\Console\Kernel as ConsoleKernel;
use Throwable;

use function Orchestra\Testbench\join_paths;

final class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [];

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    #[\Override]
    protected function commands()
    {
        if (file_exists($console = base_path(join_paths('routes', 'console.php')))) {
            require $console;
        }
    }

    /**
     * Report the exception to the exception handler.
     *
     * @param  \Throwable  $e
     * @return void
     *
     * @throws \Throwable
     */
    #[\Override]
    protected function reportException(Throwable $e)
    {
        throw $e;
    }
}
