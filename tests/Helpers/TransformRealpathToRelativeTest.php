<?php

namespace Orchestra\Testbench\Tests\Helpers;

use Orchestra\Testbench\TestCase;

use function Orchestra\Testbench\default_skeleton_path;
use function Orchestra\Testbench\package_path;
use function Orchestra\Testbench\transform_realpath_to_relative;

class TransformRealpathToRelativeTest extends TestCase
{
    /** @test */
    public function it_can_use_transform_realpath_to_relative()
    {
        $this->assertSame('Testbench.php', transform_realpath_to_relative('Testbench.php'));

        $this->assertSame(
            './src/TestCase.php',
            transform_realpath_to_relative(package_path('src', 'TestCase.php'))
        );

        $this->assertSame(
            '@laravel/composer.json',
            transform_realpath_to_relative(default_skeleton_path('composer.json'))
        );

        $this->assertSame(
            '@workbench/app/Providers/WorkbenchServiceProvider.php',
            transform_realpath_to_relative(package_path('workbench', 'app', 'Providers', 'WorkbenchServiceProvider.php'))
        );
    }

    /** @test */
    public function it_can_use_transform_realpath_to_relative_using_custom_working_path()
    {
        $this->assertSame(
            '@tests/Helpers/TransformRealpathToRelativeTest.php',
            transform_realpath_to_relative(__FILE__, package_path('tests'), '@tests')
        );
    }
}
