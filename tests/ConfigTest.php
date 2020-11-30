<?php

namespace Orchestra\Testbench\Tests;

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
    protected function defineEnvironment($app)
    {
        $app['config']->set('database.default', 'testbench');
    }

    /** @test */
    public function it_loads_config_facade()
    {
        $this->assertEquals('testbench', \Config::get('database.default'));
    }

    /** @test */
    public function it_loads_config_helper()
    {
        $this->assertEquals('testbench', config('database.default'));
    }
}
