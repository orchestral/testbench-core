<?php

namespace Orchestra\Testbench\Tests\Workbench;

use Orchestra\Testbench\Contracts\Config as ConfigContract;
use Orchestra\Testbench\Foundation\Config;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;

use function Illuminate\Filesystem\join_paths;
use function Orchestra\Testbench\package_path;
use function Orchestra\Testbench\workbench;
use function Orchestra\Testbench\workbench_path;

class HelpersTest extends TestCase
{
    #[Test]
    public function it_can_resolve_workbench()
    {
        $this->instance(ConfigContract::class, new Config([
            'workbench' => [
                'start' => '/workbench',
                'user' => 'crynobone@gmail.com',
                'guard' => 'web',
                'install' => false,
                'welcome' => false,
                'health' => false,
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
            'health' => false,
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

    #[Test]
    public function it_can_resolve_workbench_without_bound()
    {
        $this->assertSame([
            'start' => '/',
            'user' => null,
            'guard' => null,
            'install' => true,
            'welcome' => null,
            'health' => null,
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

    #[Test]
    #[Group('workbench')]
    public function it_can_resolve_workbench_path()
    {
        $this->assertSame(
            realpath(package_path('workbench/database/migrations/2013_07_26_182750_create_testbench_users_table.php')),
            workbench_path(join_paths('database', 'migrations', '2013_07_26_182750_create_testbench_users_table.php'))
        );
    }
}
