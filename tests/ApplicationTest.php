<?php

namespace Orchestra\Testbench\Tests;

use Orchestra\Testbench\Foundation\Application;
use Orchestra\Testbench\Foundation\TestbenchServiceProvider;
use PHPUnit\Framework\TestCase;

class ApplicationTest extends TestCase
{
    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array<int, \Illuminate\Support\ServiceProvider>
     */
    protected function getPackageProviders($app)
    {
        return [
            TestbenchServiceProvider::class,
        ];
    }

    /**
     * @test
     * @group core
     */
    public function it_can_create_an_application()
    {
        $app = Application::create(realpath(__DIR__.'/../laravel'));

        $this->assertInstanceOf('Illuminate\Foundation\Application', $app);
        $this->assertSame('App\\', $app->getNamespace());
    }
}
