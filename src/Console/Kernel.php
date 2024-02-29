<?php

namespace Orchestra\Testbench\Console;

use Orchestra\Testbench\Foundation\Console\Kernel as ConsoleKernel;
use Throwable;

final class Kernel extends ConsoleKernel
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
