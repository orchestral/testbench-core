<?php

namespace Orchestra\Testbench\Tests;

use Illuminate\Foundation\Application;
use function Orchestra\Testbench\laravel_version_compare;
use function Orchestra\Testbench\package_path;
use function Orchestra\Testbench\parse_environment_variables;
use function Orchestra\Testbench\phpunit_version_compare;
use Orchestra\Testbench\TestCase;
use function Orchestra\Testbench\transform_relative_path;
use PHPUnit\Runner\Version;

class HelpersTest extends TestCase
{
    /** @test */
    public function it_can_parse_environment_variables()
    {
        $given = [
            'APP_KEY' => null,
            'APP_DEBUG' => true,
            'APP_PRODUCTION' => false,
            'APP_NAME' => 'Testbench',
        ];

        $expected = [
            'APP_KEY=(null)',
            'APP_DEBUG=(true)',
            'APP_PRODUCTION=(false)',
            "APP_NAME='Testbench'",
        ];

        $this->assertSame(
            $expected, parse_environment_variables($given)
        );
    }

    /** @test */
    public function it_can_transform_relative_path()
    {
        $this->assertSame(
            realpath(__DIR__.DIRECTORY_SEPARATOR.'HelpersTest.php'),
            transform_relative_path('./HelpersTest.php', realpath(__DIR__))
        );
    }

    /** @test */
    public function it_can_package_path()
    {
        $this->assertSame(
            realpath(__DIR__.DIRECTORY_SEPARATOR.'HelpersTest.php'),
            package_path('./tests'.DIRECTORY_SEPARATOR.'HelpersTest.php')
        );
    }

    /** @test */
    public function it_can_compare_laravel_version()
    {
        $laravel = Application::VERSION === '10.x-dev' ? '10.0.0' : Application::VERSION;

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
