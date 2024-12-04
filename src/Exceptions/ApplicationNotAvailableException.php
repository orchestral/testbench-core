<?php

namespace Orchestra\Testbench\Exceptions;

use Illuminate\Contracts\Foundation\Application;
use RuntimeException;

class ApplicationNotAvailableException extends RuntimeException
{
    /**
     * Determine if the application is available before throwing an exception.
     *
     * @param  \Illuminate\Contracts\Foundation\Application|null  $app
     * @param  string  $method
     * @return true
     *
     * @throws static
     */
    public static function validate($app, string $method)
    {
        if (! $app instanceof Application) {
            throw static::make($method);
        }

        return true;
    }

    /**
     * Make new RuntimeException when application is not available.
     *
     * @param  string  $method
     * @return static
     */
    public static function make(string $method)
    {
        return new static("Application is not available to run [{$method}]");
    }
}
