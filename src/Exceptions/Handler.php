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
    protected $dontReport = [
        //
    ];

    /** {@inheritDoc} */
    protected $levels = [
        //
    ];

    /** {@inheritDoc} */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

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
