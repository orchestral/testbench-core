<?php

namespace Orchestra\Testbench\Exceptions;

use PHPUnit\Util\Filter;

class DeprecatedException extends PHPUnitErrorException
{
    /**
     * Convert exception to string.
     *
     * @return string
     */
    public function __toString(): string
    {
        return sprintf('%s'.PHP_EOL.PHP_EOL.'%s', $this->getMessage(), Filter::getFilteredStacktrace($this));
    }
}
