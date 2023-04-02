<?php

namespace Orchestra\Testbench\Tests\Foundation;

use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Orchestra\Testbench\Foundation\Console\TestFallbackCommand;
use Orchestra\Testbench\Foundation\TestbenchServiceProvider;
use Orchestra\Testbench\TestCase;

class TestbenchServiceProviderTest extends TestCase
{
    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
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
            $this->assertInstanceOf(TestFallbackCommand::class, $commands['package:test']);
        });
    }
}
