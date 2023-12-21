<?php

namespace Orchestra\Testbench\Tests\Helpers;

use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;

use function Illuminate\Filesystem\join_paths;
use function Orchestra\Testbench\package_path;

class PackagePathTest extends TestCase
{
    #[Test]
    #[Group('workbench')]
    public function it_can_use_package_path()
    {
        $this->assertSame(
            realpath(join_paths(__DIR__, 'PackagePathTest.php')),
            package_path(join_paths('./tests', 'Helpers', 'PackagePathTest.php'))
        );

        $this->assertSame(
            realpath(join_paths(__DIR__, 'PackagePathTest.php')),
            package_path(join_paths('tests', 'Helpers', 'PackagePathTest.php'))
        );

        $this->assertSame(
            realpath(join_paths(__DIR__, 'PackagePathTest.php')),
            package_path(DIRECTORY_SEPARATOR.join_paths('tests', 'Helpers', 'PackagePathTest.php'))
        );
    }
}
