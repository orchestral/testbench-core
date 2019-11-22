<?php

namespace Orchestra\Testbench\TestCase;

use Orchestra\Testbench\TestCase;

class AnnotationEnvironmentSetupTest extends TestCase
{
    /**
     * @test
     * @environment-setup firstConfig
    */
    public function it_loads_first_config_helper()
    {
        $this->assertSame('testbench', config('database.default'));
        $this->assertSame('testbench', config('testbench.one'));
        $this->assertNull(config('testbench.two'));
    }

    /**
     * @test
     * @environment-setup secondConfig
    */
    public function it_loads_second_config_helper()
    {
        $this->assertSame('testbench', config('database.default'));
        $this->assertNull(config('testbench.one'));
        $this->assertSame('testbench', config('testbench.two'));
    }

    /**
     * @test
     * @environment-setup firstConfig
     * @environment-setup secondConfig
    */
    public function it_loads_both_config_helper()
    {
        $this->assertSame('testbench', config('database.default'));
        $this->assertSame('testbench', config('testbench.one'));
        $this->assertSame('testbench', config('testbench.two'));
    }

    /**
     * Define environment setup.
     *
     * @param  Illuminate\Foundation\Application  $app
     */
    protected function firstConfig($app)
    {
        $app['config']->set('testbench.one', 'testbench');
    }

    /**
     * Define environment setup.
     *
     * @param  Illuminate\Foundation\Application  $app
     */
    protected function secondConfig($app)
    {
        $app['config']->set('testbench.two', 'testbench');
    }

    /**
     * Define environment setup.
     *
     * @param  Illuminate\Foundation\Application  $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testbench');
    }
}
