<?php

namespace Orchestra\Testbench\Tests;

use Illuminate\Foundation\Application;
use function Orchestra\Testbench\laravel_version_compare;
use function Orchestra\Testbench\phpunit_version_compare;
use PHPUnit\Framework\TestCase;
use PHPUnit\Runner\Version;

class HelpersTest extends TestCase
{
    /** @test */
    public function it_can_compare_laravel_version()
    {
        $laravel = Application::VERSION === '11.x-dev' ? '11.0.0' : Application::VERSION;

        $this->assertSame(0, laravel_version_compare($laravel));
        $this->assertTrue(laravel_version_compare($laravel, '=='));
    }

    /** @test */
    public function it_can_compare_phpunit_version()
    {
        $this->assertSame(0, phpunit_version_compare(Version::id()));
        $this->assertTrue(phpunit_version_compare(Version::id(), '=='));
    }
}
