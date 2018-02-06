<?php

namespace Orchestra\Testbench\TestCase;

use Orchestra\Testbench\TestCase;

class ConfigTest extends TestCase
{
    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testing');
    }

    /** @test */
    public function it_loads_config_facade()
    {
        $this->assertEquals('testing', \Config::get('database.default'));
    }

    /** @test */
    public function it_loads_config_helper()
    {
        $this->assertEquals('testing', config('database.default'));
    }
}
