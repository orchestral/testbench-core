<?php

namespace Orchestra\Testbench\Tests\Foundation;

use Orchestra\Testbench\Foundation\Application;
use Orchestra\Testbench\Foundation\Config;
use Orchestra\Testbench\Foundation\Env;
use PHPUnit\Framework\TestCase;

class ApplicationTest extends TestCase
{
    /**
     * Teardown the test environment.
     */
    protected function tearDown(): void
    {
        Application::flushState();
    }

    /**
     * @test
     *
     * @group core
     */
    public function it_can_create_an_application()
    {
        $testbench = new Application(realpath(__DIR__.'/../../laravel'));
        $app = $testbench->createApplication();

        $environment = Env::has('TESTBENCH_PACKAGE_TESTER') ? 'testing' : 'workbench';

        $this->assertInstanceOf('Illuminate\Foundation\Application', $app);
        $this->assertSame('App\\', $app->getNamespace());
        $this->assertEquals($environment, $app['env']);
        $this->assertSame($app['env'], $app['config']['app.env']);
        $this->assertSame($environment, $app->environment());
        $this->assertSame(Env::has('TESTBENCH_PACKAGE_TESTER'), $app->runningUnitTests());

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

        $environment = Env::has('TESTBENCH_PACKAGE_TESTER') ? 'testing' : 'workbench';

        $this->assertInstanceOf('Illuminate\Foundation\Application', $app);
        $this->assertSame('App\\', $app->getNamespace());
        $this->assertEquals($environment, $app['env']);
        $this->assertSame($app['env'], $app['config']['app.env']);
        $this->assertSame($environment, $app->environment());
        $this->assertSame(Env::has('TESTBENCH_PACKAGE_TESTER'), $app->runningUnitTests());
    }

    /**
     * @test
     *
     * @group core
     */
    public function it_can_create_an_application_using_create_from_config_helper()
    {
        $config = new Config([
            'laravel' => realpath(__DIR__.'/../../laravel'),
        ]);
        $app = Application::createFromConfig($config);

        $environment = Env::has('TESTBENCH_PACKAGE_TESTER') ? 'testing' : 'workbench';

        $this->assertInstanceOf('Illuminate\Foundation\Application', $app);
        $this->assertSame('App\\', $app->getNamespace());
        $this->assertEquals($environment, $app['env']);
        $this->assertSame($app['env'], $app['config']['app.env']);
        $this->assertSame($environment, $app->environment());
        $this->assertSame(Env::has('TESTBENCH_PACKAGE_TESTER'), $app->runningUnitTests());
    }
}
