<?php

namespace Orchestra\Testbench\Tests;

use Illuminate\Foundation\Application;
use Orchestra\Testbench\Contracts\Config as ConfigContract;
use Orchestra\Testbench\Foundation\Config;
use function Orchestra\Testbench\laravel_version_compare;
use function Orchestra\Testbench\parse_environment_variables;
use function Orchestra\Testbench\phpunit_version_compare;
use Orchestra\Testbench\TestCase;
use function Orchestra\Testbench\transform_relative_path;
use function Orchestra\Testbench\workbench;
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
            realpath(__DIR__).'/HelpersTest.php',
            transform_relative_path('./HelpersTest.php', realpath(__DIR__))
        );
    }

    /** @test */
    public function it_can_resolve_workbench()
    {
        $this->instance(ConfigContract::class, new Config([
            'workbench' => [
                'start' => '/workbench',
                'user' => 'crynobone@gmail.com',
                'guard' => 'web',
                'install' => false,
            ],
        ]));

        $this->assertSame([
            'start' => '/workbench',
            'user' => 'crynobone@gmail.com',
            'guard' => 'web',
            'install' => false,
        ], workbench());
    }

    /** @test */
    public function it_can_resolve_workbench_without_bound()
    {
        $this->assertSame([
            'start' => '/',
            'user' => null,
            'guard' => null,
            'install' => true,
        ], workbench());
    }

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
