<?php

namespace Orchestra\Testbench\Bootstrap;

use ErrorException;

final class HandleExceptions extends \Illuminate\Foundation\Bootstrap\HandleExceptions
{
    /**
     * Testbench Class.
     *
     * @var \Orchestra\Testbench\Contracts\TestCase|object|null
     */
    protected $testbench;

    /**
     * Create a new handle exceptions instance.
     *
     * @param  \Orchestra\Testbench\Contracts\TestCase|object|null  $testbench
     */
    public function __construct($testbench = null)
    {
        $this->testbench = \is_object($testbench) ? $testbench : null;
    }

    /**
     * Reports a deprecation to the "deprecations" logger.
     *
     * @param  string  $message
     * @param  string  $file
     * @param  int  $line
     * @param  int  $level
     * @return void
     */
    public function handleDeprecationError($message, $file, $line, $level = E_DEPRECATED)
    {
        if ($this->convertDeprecationsToExceptions()) {
            throw new ErrorException($message, 0, $level, $file, $line);
        }

        parent::handleDeprecationError($message, $file, $line, $level);
    }

    /**
     * Determine if deprecations should be converted to exceptions.
     *
     * @return bool
     */
    protected function convertDeprecationsToExceptions()
    {
        /** @phpstan-ignore-next-line */
        return $this->testbench?->getTestResultObject()?->getConvertDeprecationsToExceptions() ?? false;
    }
}
