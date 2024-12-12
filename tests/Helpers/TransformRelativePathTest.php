<?php

namespace Orchestra\Testbench\Tests\Helpers;

use Orchestra\Testbench\TestCase;

use function Orchestra\Testbench\transform_relative_path;

class TransformRelativePathTest extends TestCase
{
    /** @test */
    public function it_can_use_transform_relative_path()
    {
        $this->assertSame(__FILE__, transform_relative_path('./TransformRelativePathTest.php', __DIR__));
    }
}
