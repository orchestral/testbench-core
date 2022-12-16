<?php

namespace Orchestra\Testbench\Exceptions;

use PHPUnit\Framework\Error\Error;

class DeprecatedException extends Error
{
    /**
     * Convert exception to string.
     *
     * @return string
     */
    public function __toString(): string
    {
        $traces = collect(\array_slice($this->getTrace(), 3))
            ->transform(function (array $trace): ?string {
                if (! isset($trace['file']) || ! isset($trace['line'])) {
                    return null;
                }

                return sprintf('%s:%d', $trace['file'], $trace['line']);
            })->filter()
            ->values();

        return sprintf('%s'.PHP_EOL.PHP_EOL.'%s', $this->getMessage(), $traces->join(PHP_EOL));
    }
}
