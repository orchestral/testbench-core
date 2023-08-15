<?php

namespace Orchestra\Testbench\Tests;

use function Orchestra\Testbench\package_path;
use function Orchestra\Testbench\parse_environment_variables;
use Orchestra\Testbench\TestCase;
use function Orchestra\Testbench\transform_relative_path;

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
}
