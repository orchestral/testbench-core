<?php

namespace Orchestra\Testbench\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

/**
 * @internal
 */
class Handler extends ExceptionHandler
{
    /** {@inheritDoc} */
    #[\Override]
    public function report(Throwable $e, array $context = [], ?string $level = null)
    {
        parent::report($e, $context, $level);
    }

    /** {@inheritDoc} */
    #[\Override]
    public function render($request, Throwable $e)
    {
        return parent::render($request, $e);
    }
}
