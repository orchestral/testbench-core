<?php

namespace Orchestra\Testbench\Tests;

use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\RequiresPhpunit;
use PHPUnit\Framework\Attributes\Test;

#[RequiresPhpunit('>=10.1.0 <11.0.0')]
class AnnotationDefineEnvTest extends TestCase
{
    /**
     * @define-env firstConfig
     */
    #[Test]
    public function it_loads_first_config_helper()
    {
        $this->assertSame('testbench', config('database.default'));
        $this->assertSame('testbench', config('testbench.one'));
        $this->assertNull(config('testbench.two'));
    }

    /**
     * @define-env secondConfig
     */
    #[Test]
    public function it_loads_second_config_helper()
    {
        $this->assertSame('testbench', config('database.default'));
        $this->assertNull(config('testbench.one'));
        $this->assertSame('testbench', config('testbench.two'));
    }

    /**
     * @define-env firstConfig
     * @define-env secondConfig
     */
    #[Test]
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
