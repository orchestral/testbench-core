<?php

namespace Orchestra\Testbench\Exceptions;

class PHPUnitException extends Error
{
    /**
     * Get serializable trace for PHPUnit.
     *
     * @return array
     */
    public function getPHPUnitExceptionTrace(): array
    {
        return $this->getTrace();
    }
}
