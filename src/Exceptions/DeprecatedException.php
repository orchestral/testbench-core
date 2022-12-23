<?php

namespace Orchestra\Testbench\Exceptions;

use PHPUnit\Framework\Error\Error;

class DeprecatedException extends Error
{
    /**
     * List of Testbench exception/error handlers.
     *
     * @return array<int, class-string>
     */
    protected function testbenchExceptionHandlers()
    {
        return [
            \Illuminate\Foundation\Bootstrap\HandleExceptions::class,
            \Orchestra\Testbench\Bootstrap\HandleExceptions::class,
        ];
    }

    /**
     * Convert exception to string.
     *
     * @return string
     */
    public function __toString(): string
    {
        $traces = collect($this->getTrace())
            ->transform(function (array $trace): ?string {
                if ((isset($trace['class']) && \in_array($trace['class'], $this->testbenchExceptionHandlers()))
                    || ! isset($trace['file'])
                    || ! isset($trace['line'])) {
                    return null;
                }

                return sprintf('%s:%d', $trace['file'], $trace['line']);
            })->filter()
            ->values();

        return sprintf('%s'.PHP_EOL.PHP_EOL.'%s', $this->getMessage(), $traces->join(PHP_EOL));
    }
}
