<?php

namespace Orchestra\Testbench\Tests;

use function Orchestra\Testbench\transform_relative_path;
use PHPUnit\Framework\TestCase;

class HelpersTest extends TestCase
{
    /** @test */
    public function it_can_transform_relative_path()
    {
        $this->assertSame(
            realpath(__DIR__.'/HelpersTest.php'),
            transform_relative_path('./HelpersTest.php', realpath(__DIR__))
        );
    }
}
