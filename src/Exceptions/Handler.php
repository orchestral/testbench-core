<?php

namespace Orchestra\Testbench\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /** {@inheritDoc} */
    #[\Override]
    public function report(Throwable $e)
    {
        parent::report($e);
    }

    /** {@inheritDoc} */
    #[\Override]
    public function render($request, Throwable $e)
    {
        return parent::render($request, $e);
    }
}
