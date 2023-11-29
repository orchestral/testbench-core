<?php

namespace Orchestra\Testbench\Tests;

use Orchestra\Testbench\Attributes\Define;
use Orchestra\Testbench\Attributes\DefineEnvironment;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;

class AttributeEnvironmentSetupTest extends TestCase
{
    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        $this->usesTestingAttribute(new Define('env', 'globalConfig'));

        parent::setUp();
    }

    #[Test]
    #[Define('env', 'firstConfig')]
    public function it_loads_first_config_helper()
    {
        $this->assertSame('testbench', config('database.default'));
        $this->assertSame('testbench', config('testbench.global'));
        $this->assertSame('testbench', config('testbench.one'));
        $this->assertNull(config('testbench.two'));
    }

    #[Test]
    #[DefineEnvironment('secondConfig')]
    public function it_loads_second_config_helper()
    {
        $this->assertSame('testbench', config('database.default'));
        $this->assertSame('testbench', config('testbench.global'));
        $this->assertNull(config('testbench.one'));
        $this->assertSame('testbench', config('testbench.two'));
    }

    #[Test]
    #[DefineEnvironment('firstConfig')]
    #[DefineEnvironment('secondConfig')]
    public function it_loads_both_config_helper()
    {
        $this->assertSame('testbench', config('database.default'));
        $this->assertSame('testbench', config('testbench.global'));
        $this->assertSame('testbench', config('testbench.one'));
        $this->assertSame('testbench', config('testbench.two'));
    }

    /**
     * Define environment setup.
     *
     * @param  Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function globalConfig($app)
    {
        $app['config']->set('testbench.global', 'testbench');
    }

    /**
     * Define environment setup.
     *
     * @param  Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function firstConfig($app)
    {
        $app['config']->set('testbench.one', 'testbench');
    }

    /**
     * Define environment setup.
     *
     * @param  Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function secondConfig($app)
    {
        $app['config']->set('testbench.two', 'testbench');
    }

    /**
     * Define environment setup.
     *
     * @param  Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function defineEnvironment($app)
    {
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
        ]);
    }
}
