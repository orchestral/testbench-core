<?php

namespace Orchestra\Testbench\Console;

use Exception;
use Orchestra\Testbench\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [];

    /**
     * Report the exception to the exception handler.
     *
     * @param  \Exception  $e
     *
     * @throws \Exception
     *
     * @return void
     */
    protected function reportException(Exception $e)
    {
        throw $e;
    }
}
