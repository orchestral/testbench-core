<?php

namespace Orchestra\Testbench\Bootstrap;

use ErrorException;
use Illuminate\Log\LogManager;
use Illuminate\Support\Env;
use PHPUnit\Framework\Error\Deprecated;

final class HandleExceptions extends \Illuminate\Foundation\Bootstrap\HandleExceptions
{
    /**
     * Testbench Class.
     *
     * @var \PHPUnit\Framework\TestCase|null
     */
    protected $testbench;

    /**
     * Create a new exception handler instance.
     *
     * @param  \PHPUnit\Framework\TestCase|null  $testbench
     */
    public function __construct($testbench = null)
    {
        $this->testbench = $testbench;
    }

    /**
     * Report PHP deprecations, or convert PHP errors to ErrorException instances.
     *
     * @param  int  $level
     * @param  string  $message
     * @param  string  $file
     * @param  int  $line
     * @param  array  $context
     * @return void
     *
     * @throws \ErrorException
     */
    public function handleError($level, $message, $file = '', $line = 0, $context = [])
    {
        if (__FILE__ === $file) {
            $trace = debug_backtrace(\DEBUG_BACKTRACE_IGNORE_ARGS, 3);
            $file = $trace[2]['file'] ?? $file;
            $line = $trace[2]['line'] ?? $line;
        }

        if ($this->isDeprecation($level)) {
            return $this->handleDeprecationError($message, $file, $line, $level);
        } elseif (error_reporting() & $level) {
            throw new ErrorException($message, 0, $level, $file, $line);
        }
    }

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
     * @throws \PHPUnit\Framework\Error\Deprecated
     */
    public function handleDeprecationError($message, $file, $line, $level = E_DEPRECATED)
    {
        parent::handleDeprecationError($message, $file, $line, $level);

        $testbenchConvertDeprecationsToExceptions = Env::get('TESTBENCH_CONVERT_DEPRECATIONS_TO_EXCEPTIONS');

        if ($testbenchConvertDeprecationsToExceptions === true) {
            with(new ErrorException($message, 0, $level, $file, $line), function ($e) {
                $this->renderForConsole($e);

                throw $e;
            });
        }

        /** @var \PHPUnit\Framework\TestResult|null $testResult */
        $testResult = $this->testbench?->getTestResultObject();

        /** @var bool $convertDeprecationsToExceptions */
        $convertDeprecationsToExceptions = $testResult?->getConvertDeprecationsToExceptions() ?? false;

        if ($testbenchConvertDeprecationsToExceptions !== false && $convertDeprecationsToExceptions === true) {
            with(new Deprecated($message, $level, $file, $line), function ($e) {
                $this->renderForConsole($e);

                throw $e;
            });
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
