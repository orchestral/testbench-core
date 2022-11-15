<?php

namespace Orchestra\Testbench\Tests\Foundation;

use Orchestra\Testbench\Foundation\Config;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    /** @test */
    public function it_can_load_configuration_file()
    {
        $config = Config::loadFromYaml(__DIR__.'/stubs/');

        $this->assertNull($config['laravel']);
        $this->assertSame([], $config['env']);
        $this->assertSame([
            'Orchestra\Testbench\Foundation\TestbenchServiceProvider',
        ], $config['providers']);
        $this->assertSame([], $config['dont-discover']);

        $this->assertSame([
            'providers' => [
                'Orchestra\Testbench\Foundation\TestbenchServiceProvider',
            ],
            'dont-discover' => [],
        ], $config->getExtraAttributes());
    }
}
