<?php

namespace Orchestra\Testbench\Tests;

use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase;
use PHPUnit\Runner\Version;

use function Orchestra\Testbench\laravel_version_compare;
use function Orchestra\Testbench\package_path;
use function Orchestra\Testbench\parse_environment_variables;
use function Orchestra\Testbench\phpunit_version_compare;
use function Orchestra\Testbench\transform_relative_path;

class HelpersTest extends TestCase
{
    /** @test */
    public function it_can_compare_laravel_version()
    {
        $this->assertSame(0, laravel_version_compare(Application::VERSION));
        $this->assertTrue(laravel_version_compare(Application::VERSION, '=='));
    }

    /** @test */
    public function it_can_compare_phpunit_version()
    {
        $this->assertSame(0, phpunit_version_compare(Version::id()));
        $this->assertTrue(phpunit_version_compare(Version::id(), '=='));
    }
}
