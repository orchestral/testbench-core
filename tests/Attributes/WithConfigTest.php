<?php

namespace Orchestra\Testbench\Tests\Attributes;

use Orchestra\Testbench\Attributes\WithConfig;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;

class WithConfigTest extends TestCase
{
    #[Test]
    #[WithConfig('testbench.attribute', true)]
    public function it_can_resolve_defined_configuration()
    {
        $this->assertSame(true, config('testbench.attribute'));
    }

    #[Test]
    public function it_does_not_persist_defined_configuration_between_tests()
    {
        $this->assertNull(config('testbench.attribute'));
    }
}
