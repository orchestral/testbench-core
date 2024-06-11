<?php

namespace Orchestra\Testbench\Tests\Exceptions;

use Orchestra\Testbench\Exceptions\DeprecatedException;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;

class DeprecatedExceptionTest extends TestCase
{
    #[Test]
    public function it_can_be_converted_to_string()
    {
        $exception = new DeprecatedException('Error', 1, __FILE__, 3);

        $this->assertStringContainsString('Error'.PHP_EOL.PHP_EOL.__FILE__.':3', (string) $exception);
    }
}
