<?php

namespace Orchestra\Testbench\Tests\Helpers;

use Orchestra\Testbench\TestCase;

use function Orchestra\Testbench\package_path;

class PackagePathTest extends TestCase
{
    /**
     * @test
     *
     * @group workbench
     */
    public function it_can_use_package_path()
    {
        $this->assertSame(realpath(__DIR__.'/../../'), package_path());
        $this->assertSame(implode('', [realpath(__DIR__.'/../../'), DIRECTORY_SEPARATOR]), package_path(DIRECTORY_SEPARATOR));

        $this->assertSame(
            realpath(__DIR__.DIRECTORY_SEPARATOR.'PackagePathTest.php'),
            package_path('./tests'.DIRECTORY_SEPARATOR.'Helpers'.DIRECTORY_SEPARATOR.'PackagePathTest.php')
        );

        $this->assertSame(
            realpath(__DIR__.DIRECTORY_SEPARATOR.'PackagePathTest.php'),
            package_path('./tests'.DIRECTORY_SEPARATOR.'Helpers'.DIRECTORY_SEPARATOR.'PackagePathTest.php')
        );

        $this->assertSame(
            realpath(__DIR__.DIRECTORY_SEPARATOR.'PackagePathTest.php'),
            package_path(DIRECTORY_SEPARATOR.'tests'.DIRECTORY_SEPARATOR.'Helpers'.DIRECTORY_SEPARATOR.'PackagePathTest.php')
        );
    }
}
