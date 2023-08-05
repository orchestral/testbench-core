<?php

namespace Orchestra\Testbench\Tests\Foundation;

use Illuminate\Database\Events\DatabaseRefreshed;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Mockery as m;
use NunoMaduro\Collision\Adapters\Laravel\Commands\TestCommand as CollisionTestCommand;
use Orchestra\Testbench\Contracts\Config as ConfigContract;
use Orchestra\Testbench\Foundation\Config;
use Orchestra\Testbench\Foundation\Console\TestCommand;
use Orchestra\Testbench\Foundation\Console\TestFallbackCommand;
use Orchestra\Testbench\Foundation\TestbenchServiceProvider;
use Orchestra\Testbench\TestCase;

/**
 * @covers \Orchestra\Testbench\Foundation\TestbenchServiceProvider
 */
class TestbenchServiceProviderTest extends TestCase
{
    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array<int, class-string<\Illuminate\Support\ServiceProvider>>
     */
    protected function getPackageProviders($app)
    {
        return [
            TestbenchServiceProvider::class,
        ];
    }

    /** @test */
    public function it_register_the_correct_command()
    {
        tap($this->app[ConsoleKernel::class]->all(), function ($commands) {
            $this->assertArrayHasKey('package:test', $commands);
            $this->assertInstanceOf(
                $this->isCollisionDependenciesInstalled() ? TestCommand::class : TestFallbackCommand::class,
                $commands['package:test']
            );
        });
    }

    /**
     * @test
     */
    public function it_can_seed_database_after_refreshed()
    {
        $this->instance('\TestbenchDatabaseSeeder', $seeder = m::mock('TestbenchDatabaseSeeder'));
        $this->instance(ConfigContract::class, new Config([
            'seeders' => ['\TestbenchDatabaseSeeder'],
        ]));

        $seeder->shouldReceive('setContainer')->once()->with(app())->andReturnSelf()
            ->shouldReceive('setCommand')->once()->andReturnSelf()
            ->shouldReceive('__invoke')->once()->andReturnNull();

        app('events')->dispatch(new DatabaseRefreshed());
    }

    /**
     * Check if the parallel dependencies are installed.
     *
     * @return bool
     */
    protected function isCollisionDependenciesInstalled(): bool
    {
        return class_exists(CollisionTestCommand::class);
    }
}
