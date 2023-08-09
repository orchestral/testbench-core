<?php

namespace Orchestra\Testbench\Tests;

use Illuminate\Foundation\Application;
use Orchestra\Testbench\Foundation\Application as Testbench;
use PHPUnit\Framework\TestCase;

class ApplicationTest extends TestCase
{
    /**
     * @test
     *
     * @group core
     */
    public function it_can_create_an_application()
    {
        $testbench = new Testbench(realpath(__DIR__.'/../laravel'));
        $app = $testbench->createApplication();

        $this->assertInstanceOf(Application::class, $app);
        $this->assertSame('App\\', $app->getNamespace());
        $this->assertSame('testing', $app->environment());
        $this->assertEquals('testing', $app['env']);
        $this->assertSame($app['env'], $app['config']['app.env']);

        $this->assertFalse($testbench->isRunningTestCase());
    }
}
