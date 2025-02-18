<?php

namespace Orchestra\Testbench\Tests\Helpers;

use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;

use function Orchestra\Sidekick\join_paths;
use function Orchestra\Testbench\default_skeleton_path;
use function Orchestra\Testbench\package_path;
use function Orchestra\Testbench\transform_realpath_to_relative;

#[Group('core')]
class TransformRealpathToRelativeTest extends TestCase
{
    #[Test]
    public function it_can_use_transform_realpath_to_relative()
    {
        $this->assertSame('Testbench.php', transform_realpath_to_relative('Testbench.php'));

        $this->assertSame(
            join_paths('.', 'src', 'TestCase.php'),
            transform_realpath_to_relative(package_path('src', 'TestCase.php'))
        );

        $this->assertSame(
            join_paths('@laravel', 'composer.json'),
            transform_realpath_to_relative(default_skeleton_path('composer.json'))
        );

        $this->assertSame(
            join_paths('@workbench', 'app', 'Providers', 'WorkbenchServiceProvider.php'),
            transform_realpath_to_relative(package_path('workbench', 'app', 'Providers', 'WorkbenchServiceProvider.php'))
        );
    }

    #[Test]
    public function it_can_use_transform_realpath_to_relative_using_custom_working_path()
    {
        $this->assertSame(
            join_paths('@tests', 'Helpers', 'TransformRealpathToRelativeTest.php'),
            transform_realpath_to_relative(__FILE__, package_path('tests'), '@tests')
        );
    }
}
