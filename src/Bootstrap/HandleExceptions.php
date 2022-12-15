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
     * @var \Orchestra\Testbench\TestCase|null
     */
    protected $testbench;

    /**
     * Create a new exception handler instance.
     *
     * @param  \Orchestra\Testbench\TestCase|null  $testbench
     */
    public function __construct($testbench = null)
    {
        $this->testbench = $testbench;
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

        if (Env::get('TESTBENCH_CONVERT_DEPRECATIONS_TO_EXCEPTIONS')) {
            throw new ErrorException($message, 0, $level, $file, $line);
        } else {
            /** @var \PHPUnit\Framework\TestResult|null $testResult */
            $testResult = $this->testbench?->getTestResultObject();

            /** @var bool $convertDeprecationsToExceptions */
            $convertDeprecationsToExceptions = $testResult?->getConvertDeprecationsToExceptions() ?? false;

            if ($convertDeprecationsToExceptions === true) {
                throw new Deprecated($message, $level, $file, $line);
            }
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
