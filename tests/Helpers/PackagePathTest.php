<?php

namespace Orchestra\Testbench\Tests\Helpers;

use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;

use function Illuminate\Filesystem\join_paths;
use function Orchestra\Testbench\package_path;

#[Group('workbench')]
class PackagePathTest extends TestCase
{
    #[Test]
    public function it_can_use_package_path()
    {
        $this->assertSame(realpath(__DIR__.'/../../'), package_path());
        $this->assertSame(implode('', [realpath(__DIR__.'/../../'), DIRECTORY_SEPARATOR]), package_path(DIRECTORY_SEPARATOR));
    }

    #[Test]
    #[DataProvider(('pathDataProvider'))]
    public function it_can_resolve_correct_package_path(string $path)
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

        $this->assertSame(
            realpath(join_paths(__DIR__, 'PackagePathTest.php')),
            package_path(join_paths('tests', 'Helpers', 'PackagePathTest.php'))
        );
    }

    public static function pathDataProvider()
    {
        yield [package_path('tests'.DIRECTORY_SEPARATOR.'Helpers'.DIRECTORY_SEPARATOR.'PackagePathTest.php')];
        yield [package_path('./tests'.DIRECTORY_SEPARATOR.'Helpers'.DIRECTORY_SEPARATOR.'PackagePathTest.php')];
        yield [package_path(DIRECTORY_SEPARATOR.'tests'.DIRECTORY_SEPARATOR.'Helpers'.DIRECTORY_SEPARATOR.'PackagePathTest.php')];

        yield [package_path('tests', 'Helpers', 'PackagePathTest.php')];
        yield [package_path(['tests', 'Helpers', 'PackagePathTest.php'])];
        yield [package_path('./tests', 'Helpers', 'PackagePathTest.php')];
        yield [package_path(['./tests', 'Helpers', 'PackagePathTest.php'])];
    }
}
