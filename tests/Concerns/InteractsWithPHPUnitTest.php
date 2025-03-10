<?php

namespace Orchestra\Testbench\Tests\Concerns;

use Orchestra\Testbench\TestCase;

class InteractsWithPHPUnitTest extends TestCase
{
    /** @test */
    public function it_can_resolve_the_correct_class_and_method_name()
    {
        $this->assertSame(__CLASS__, $this->resolvePhpUnitTestClassName());
        $this->assertSame('it_can_resolve_the_correct_class_and_method_name', $this->resolvePhpUnitTestMethodName());
    }
}
