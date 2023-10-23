<?php

namespace Orchestra\Testbench\Tests\Foundation;

use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use NunoMaduro\Collision\Adapters\Laravel\Commands\TestCommand as CollisionTestCommand;
use Orchestra\Testbench\Foundation\Console\TestCommand;
use Orchestra\Testbench\Foundation\Console\TestFallbackCommand;
use Orchestra\Testbench\Foundation\TestbenchServiceProvider;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(TestbenchServiceProvider::class)]
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

    #[Test]
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
     * Check if the parallel dependencies are installed.
     *
     * @return bool
     */
    protected function isCollisionDependenciesInstalled(): bool
    {
        return class_exists(CollisionTestCommand::class);
    }
}
