<?php

namespace Orchestra\Testbench\Exceptions;

use Illuminate\Foundation\Bootstrap\HandleExceptions as IlluminateHandleExceptions;
use Orchestra\Testbench\Bootstrap\HandleExceptions;
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
        $traces = collect($this->getTrace())
            ->transform(function (array $trace): ?string {
                $excluded = [HandleExceptions::class, IlluminateHandleExceptions::class];

                if ((isset($trace['class']) && \in_array($trace['class'], $excluded))
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
