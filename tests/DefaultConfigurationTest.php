<?php

namespace Orchestra\Testbench\Tests;

use Illuminate\Support\Env;
use Orchestra\Testbench\TestCase;

class DefaultConfigurationTest extends TestCase
{
    /** @test */
    public function it_populate_expected_debug_config()
    {
        $this->assertFalse($this->app['config']['app.debug']);
    }

    /** @test */
    public function it_populate_expected_testing_config()
    {
        tap($this->app['config']['database.connections.testing'], function ($config) {
            $this->assertTrue(isset($config));
            $this->assertEquals([
                'driver' => 'sqlite',
                'database' => ':memory:',
                'foreign_key_constraints' => false,
            ], $config);
        });
    }

    /** @test */
    public function it_populate_expected_cache_defaults()
    {
        $this->assertEquals((Env::get('TESTBENCH_PACKAGE_TESTER') === true ? 'file' : 'array'), $this->app['config']['cache.default']);
    }

    /** @test */
    public function it_populate_expected_session_defaults()
    {
        $this->assertEquals((Env::get('TESTBENCH_PACKAGE_TESTER') === true ? 'file' : 'array'), $this->app['config']['session.driver']);
    }
}
