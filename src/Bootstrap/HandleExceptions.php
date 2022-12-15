<?php

namespace Orchestra\Testbench\Bootstrap;

use ErrorException;
use Illuminate\Log\LogManager;
use Illuminate\Support\Env;

final class HandleExceptions extends \Illuminate\Foundation\Bootstrap\HandleExceptions
{
    /**
     * Reports a deprecation to the "deprecations" logger.
     *
     * @param  string  $message
     * @param  string  $file
     * @param  int  $line
     * @param  int  $level
     * @return void
     *
     * @throws \ErrorException
     */
    public function handleDeprecationError($message, $file, $line, $level = E_DEPRECATED)
    {
        parent::handleDeprecationError($message, $file, $line, $level);

        if (Env::get('TESTBENCH_CONVERT_DEPRECATIONS_TO_EXCEPTIONS')) {
            throw new ErrorException($message, 0, $level, $file, $line);
        }
    }

    /**
     * Determine if deprecation error should be ignored.
     *
     * @return bool
     */
    protected function shouldIgnoreDeprecationErrors()
    {
        return ! class_exists(LogManager::class)
            || ! static::$app->hasBeenBootstrapped();
    }
}
