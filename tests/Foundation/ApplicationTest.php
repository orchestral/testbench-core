<?php

namespace Orchestra\Testbench\Tests\Foundation;

use Orchestra\Testbench\Foundation\Application;
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
        $testbench = new Application(realpath(__DIR__.'/../../laravel'));
        $app = $testbench->createApplication();

        $this->assertInstanceOf('Illuminate\Foundation\Application', $app);
        $this->assertSame('App\\', $app->getNamespace());
        $this->assertEquals('workbench', $app['env']);
        $this->assertSame($app['env'], $app['config']['app.env']);
        $this->assertSame('workbench', $app->environment());
        $this->assertFalse($app->runningUnitTests());

        $this->assertFalse($testbench->isRunningTestCase());
    }

    /**
     * @test
     *
     * @group core
     */
    public function it_can_create_an_application_using_create_helper()
    {
        $app = Application::create(realpath(__DIR__.'/../../laravel'));

        $this->assertInstanceOf('Illuminate\Foundation\Application', $app);
        $this->assertSame('App\\', $app->getNamespace());
        $this->assertEquals('workbench', $app['env']);
        $this->assertSame($app['env'], $app['config']['app.env']);
        $this->assertSame('workbench', $app->environment());
        $this->assertFalse($app->runningUnitTests());
    }
}
