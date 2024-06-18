<?php

namespace Orchestra\Testbench\Tests\Workbench;

use Orchestra\Testbench\Contracts\Config as ConfigContract;
use Orchestra\Testbench\Foundation\Config;
use Orchestra\Testbench\TestCase;

use function Orchestra\Testbench\workbench;
use function Orchestra\Testbench\workbench_path;

class HelpersTest extends TestCase
{
    /** @test */
    public function it_can_resolve_workbench()
    {
        $this->instance(ConfigContract::class, new Config([
            'workbench' => [
                'start' => '/workbench',
                'user' => 'crynobone@gmail.com',
                'guard' => 'web',
                'install' => false,
                'welcome' => false,
                'discovers' => [
                    'web' => true,
                ],
            ],
        ]));

        $this->assertSame([
            'start' => '/workbench',
            'user' => 'crynobone@gmail.com',
            'guard' => 'web',
            'install' => false,
            'welcome' => false,
            'sync' => [],
            'build' => [],
            'assets' => [],
            'discovers' => [
                'config' => false,
                'factories' => false,
                'web' => true,
                'api' => false,
                'commands' => false,
                'components' => false,
                'views' => false,
            ],
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
            'welcome' => null,
            'sync' => [],
            'build' => [],
            'assets' => [],
            'discovers' => [
                'config' => false,
                'factories' => false,
                'web' => false,
                'api' => false,
                'commands' => false,
                'components' => false,
                'views' => false,
            ],
        ], workbench());
    }

    /**
     * @test
     *
     * @group workbench
     */
    public function it_can_resolve_workbench_path()
    {
        $this->assertSame(
            realpath(__DIR__.'/../../workbench/database/migrations/2013_07_26_182750_create_testbench_users_table.php'),
            workbench_path('database'.DIRECTORY_SEPARATOR.'migrations'.DIRECTORY_SEPARATOR.'2013_07_26_182750_create_testbench_users_table.php')
        );
    }
}
