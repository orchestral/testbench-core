<?php

namespace Orchestra\Testbench\Exceptions;

if (! class_exists(\PHPUnit\Runner\Version::class)) {
    return;
}

/**
 * @TODO To be removed and use `Illuminate\Foundation\Testing\Concerns\InteractsWithNotSuccessfulTests`.
 */
if (\intval(substr(\PHPUnit\Runner\Version::id(), 0, 1)) === 1) {
    class PHPUnitErrorException extends \PHPUnit\Framework\Exception
    {
        public function __construct(string $message, int $code, string $file, int $line, \Exception $previous = null)
        {
            parent::__construct($message, $code, $previous);

            $this->file = $file;
            $this->line = $line;
        }

        /**
         * Get serializable trace for PHPUnit.
         *
         * @return array
         */
        public function getPHPUnitExceptionTrace(): array
        {
            return $this->serializableTrace;
        }
    }
} else {
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
}
