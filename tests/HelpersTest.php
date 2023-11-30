<?php

namespace Orchestra\Testbench\Tests;

use Orchestra\Testbench\TestCase;
use Symfony\Component\Console\Command\Command;

use function Orchestra\Testbench\artisan;
use function Orchestra\Testbench\package_path;
use function Orchestra\Testbench\parse_environment_variables;
use function Orchestra\Testbench\transform_relative_path;

class HelpersTest extends TestCase
{
    /** @test */
    public function it_can_run_artisan_command()
    {
        $this->assertSame(Command::SUCCESS, artisan($this, 'env'));
        $this->assertSame(Command::SUCCESS, artisan($this->app, 'env'));
    }

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
    public function it_can_use_transform_relative_path()
    {
        $this->assertSame(
            realpath(__DIR__.DIRECTORY_SEPARATOR.'HelpersTest.php'),
            transform_relative_path('./HelpersTest.php', realpath(__DIR__))
        );
    }

    /**
     * @test
     *
     * @group workbench
     */
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
}
