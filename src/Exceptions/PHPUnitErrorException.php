<?php

namespace Orchestra\Testbench\Exceptions;

class PHPUnitErrorException extends \PHPUnit\Framework\Error\Error
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
