<?php

namespace Orchestra\Testbench\Tests;

use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Runner\Version;

use function Orchestra\Testbench\laravel_version_compare;
use function Orchestra\Testbench\phpunit_version_compare;

class HelpersTest extends TestCase
{
    #[Test]
    public function it_can_compare_laravel_version()
    {
        $laravel = Application::VERSION === '11.x-dev' ? '11.0.0' : Application::VERSION;

        $this->assertSame(0, laravel_version_compare($laravel));
        $this->assertTrue(laravel_version_compare($laravel, '=='));
    }

    #[Test]
    public function it_can_compare_phpunit_version()
    {
        $phpunit = Version::id() === '11.5-dev' ? '11.5.0' : Version::id();

        $this->assertSame(0, phpunit_version_compare($phpunit));
        $this->assertTrue(phpunit_version_compare($phpunit, '=='));
    }
}
