<?php

namespace Orchestra\Testbench\Tests\Exceptions;

use Throwable;

class SilentConsoleHandler extends \Orchestra\Testbench\Exceptions\Handler
{
    /** {@inheritDoc} */
    #[\Override]
    public function renderForConsole($output, Throwable $e)
    {
        //
    }
}
