<?php

namespace Orchestra\Testbench\Tests\Integrations;

use Illuminate\Support\Facades\Config;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ConfigTest extends TestCase
{
    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
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

    #[Test]
    public function it_loads_config_facade()
    {
        $this->assertEquals('testbench', Config::get('database.default'));
    }

    #[Test]
    public function it_loads_config_helper()
    {
        $this->assertEquals('testbench', config('database.default'));
    }
}
