<?php

namespace Orchestra\Testbench\Tests\Foundation;

use Orchestra\Testbench\Foundation\Application;
use Orchestra\Testbench\Foundation\Config;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ApplicationTest extends TestCase
{
    #[Test]
    #[Group('core')]
    public function it_can_create_an_application()
    {
        $testbench = new Application(realpath(__DIR__.'/../../laravel'));
        $app = $testbench->createApplication();

        $this->assertInstanceOf('Illuminate\Foundation\Application', $app);
        $this->assertSame('App\\', $app->getNamespace());
        $this->assertEquals('testing', $app['env']);
        $this->assertSame($app['env'], $app['config']['app.env']);
        $this->assertSame('testing', $app->environment());
        $this->assertTrue($app->runningUnitTests());

        $this->assertFalse($testbench->isRunningTestCase());
    }

    #[Test]
    #[Group('core')]
    public function it_can_create_an_application_using_create_helper()
    {
        $app = Application::create(realpath(__DIR__.'/../../laravel'));

        $this->assertInstanceOf('Illuminate\Foundation\Application', $app);
        $this->assertSame('App\\', $app->getNamespace());
        $this->assertEquals('testing', $app['env']);
        $this->assertSame($app['env'], $app['config']['app.env']);
        $this->assertSame('testing', $app->environment());
        $this->assertTrue($app->runningUnitTests());
    }

    #[Test]
    #[Group('core')]
    public function it_can_create_an_application_using_create_from_config_helper()
    {
        $config = new Config([
            'laravel' => realpath(__DIR__.'/../../laravel'),
        ]);
        $app = Application::createFromConfig($config);

        $this->assertInstanceOf('Illuminate\Foundation\Application', $app);
        $this->assertSame('App\\', $app->getNamespace());
        $this->assertEquals('testing', $app['env']);
        $this->assertSame($app['env'], $app['config']['app.env']);
        $this->assertSame('testing', $app->environment());
        $this->assertTrue($app->runningUnitTests());
    }
}
