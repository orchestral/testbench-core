<?php

namespace Orchestra\Testbench\Tests;

use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Runner\Version;

use function Orchestra\Testbench\laravel_version_compare;
use function Orchestra\Testbench\package_path;
use function Orchestra\Testbench\parse_environment_variables;
use function Orchestra\Testbench\phpunit_version_compare;
use function Orchestra\Testbench\transform_relative_path;

class HelpersTest extends TestCase
{
    #[Test]
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

    #[Test]
    public function it_can_use_transform_relative_path()
    {
        $this->assertSame(
            realpath(__DIR__.DIRECTORY_SEPARATOR.'HelpersTest.php'),
            transform_relative_path('./HelpersTest.php', realpath(__DIR__))
        );
    }

    #[Test]
    #[Group('workbench')]
    public function it_can_use_package_path()
    {
        $this->assertSame(
            realpath(__DIR__.DIRECTORY_SEPARATOR.'HelpersTest.php'),
            package_path('./tests'.DIRECTORY_SEPARATOR.'HelpersTest.php')
        );

        $this->assertSame(
            realpath(__DIR__.DIRECTORY_SEPARATOR.'HelpersTest.php'),
            package_path('tests'.DIRECTORY_SEPARATOR.'HelpersTest.php')
        );

        $this->assertSame(
            realpath(__DIR__.DIRECTORY_SEPARATOR.'HelpersTest.php'),
            package_path(DIRECTORY_SEPARATOR.'tests'.DIRECTORY_SEPARATOR.'HelpersTest.php')
        );
    }

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
        $this->assertSame(0, phpunit_version_compare(Version::id()));
        $this->assertTrue(phpunit_version_compare(Version::id(), '=='));
    }
}
