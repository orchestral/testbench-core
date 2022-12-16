<?php

namespace Orchestra\Testbench\Tests\Exceptions;

use Throwable;

class SilentConsoleHandler extends \Orchestra\Testbench\Exceptions\Handler
{
    /**
     * Render an exception to the console.
     *
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     * @param  \Throwable  $e
     * @return void
     *
     * @internal This method is not meant to be used or overwritten outside the framework.
     */
    public function renderForConsole($output, Throwable $e)
    {

    }
}
