<?php

namespace Orchestra\Testbench\Tests;

use Illuminate\Foundation\Application;
use function Orchestra\Testbench\laravel_version_compare;
use function Orchestra\Testbench\phpunit_version_compare;
use function Orchestra\Testbench\transform_relative_path;
use PHPUnit\Framework\TestCase;
use PHPUnit\Runner\Version;

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
