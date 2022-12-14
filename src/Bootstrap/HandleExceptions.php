<?php

namespace Orchestra\Testbench\Bootstrap;

final class HandleExceptions extends \Illuminate\Foundation\Bootstrap\HandleExceptions
{
    /**
     * Determine if deprecation error should be ignored.
     *
     * @return bool
     */
    protected function shouldIgnoresDeprecationError()
    {
        return ! class_exists(LogManager::class)
            || ! static::$app->hasBeenBootstrapped();
    }
}
