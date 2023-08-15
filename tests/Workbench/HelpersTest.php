<?php

namespace Orchestra\Testbench\Tests\Workbench;

use Orchestra\Testbench\Contracts\Config as ConfigContract;
use Orchestra\Testbench\Foundation\Config;
use Orchestra\Testbench\TestCase;
use function Orchestra\Testbench\workbench;

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
            ],
        ]));

        $this->assertSame([
            'start' => '/workbench',
            'user' => 'crynobone@gmail.com',
            'guard' => 'web',
            'install' => false,
            'sync' => [],
            'build' => [],
            'assets' => [],
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
            'sync' => [],
            'build' => [],
            'assets' => [],
        ], workbench());
    }
}
