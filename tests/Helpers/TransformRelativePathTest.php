<?php

namespace Orchestra\Testbench\Tests\Helpers;

use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;

use function Illuminate\Filesystem\join_paths;
use function Orchestra\Testbench\transform_relative_path;

class TransformRelativePathTest extends TestCase
{
    #[Test]
    public function it_can_use_transform_relative_path()
    {
        $this->assertSame(
            realpath(join_paths(__DIR__, 'TransformRelativePathTest.php')),
            transform_relative_path('./TransformRelativePathTest.php', realpath(__DIR__))
        );
    }
}
