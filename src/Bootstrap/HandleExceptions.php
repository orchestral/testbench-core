<?php

namespace Orchestra\Testbench\Bootstrap;

use Illuminate\Log\LogManager;
use Illuminate\Support\Env;
use Orchestra\Testbench\Exceptions\DeprecatedException;

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

        $error = new DeprecatedException($message, $level, $file, $line);

        if ($testbenchConvertDeprecationsToExceptions === true) {
            throw $error;
        }

        if ($testbenchConvertDeprecationsToExceptions !== false && $this->getPhpUnitConvertDeprecationsToExceptions() === true) {
            throw $error;
        }
    }

    /**
     * Get PHPUnit convert deprecations to exceptions from TestResult.
     *
     * @phpunit-overrides
     *
     * @return bool
     */
    protected function getPhpUnitConvertDeprecationsToExceptions(): bool
    {
        /** @var \PHPUnit\Framework\TestResult|null $testResult */
        $testResult = $this->testbench?->getTestResultObject();

        return $testResult?->getConvertDeprecationsToExceptions() ?? false;
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
