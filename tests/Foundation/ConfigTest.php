<?php

namespace Orchestra\Testbench\Tests\Foundation;

use Orchestra\Testbench\Foundation\Config;
use Orchestra\Testbench\Tests\Fixtures\Providers\ChildServiceProvider;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    /** @test */
    public function it_can_load_configuration_file()
    {
        $config = Config::loadFromYaml(__DIR__.'/stubs/');

        $this->assertNull($config['laravel']);
        $this->assertSame([
            'APP_DEBUG=(false)',
        ], $config['env']);
        $this->assertSame([
            'Orchestra\Testbench\Foundation\TestbenchServiceProvider',
        ], $config['providers']);
        $this->assertSame([], $config['dont-discover']);

        $this->assertSame([
            'env' => [
                'APP_DEBUG=(false)',
            ],
            'bootstrappers' => [],
            'providers' => [
                'Orchestra\Testbench\Foundation\TestbenchServiceProvider',
            ],
            'dont-discover' => [],
        ], $config->getExtraAttributes());
    }

    /** @test */
    public function it_can_add_additional_providers_to_configuration_file()
    {
        $config = Config::loadFromYaml(__DIR__.'/stubs/');

        $this->assertSame([
            'Orchestra\Testbench\Foundation\TestbenchServiceProvider',
        ], $config['providers']);

        $config->addProviders([
            'Orchestra\Testbench\Tests\Fixtures\Providers\ChildServiceProvider',
        ]);

        $this->assertSame([
            'Orchestra\Testbench\Foundation\TestbenchServiceProvider',
            'Orchestra\Testbench\Tests\Fixtures\Providers\ChildServiceProvider',
        ], $config['providers']);
    }

    /** @test */
    public function it_cant_add_duplicated_providers_to_configuration_file()
    {
        $config = Config::loadFromYaml(__DIR__.'/stubs/');

        $this->assertSame([
            'Orchestra\Testbench\Foundation\TestbenchServiceProvider',
        ], $config['providers']);

        $config->addProviders([
            'Orchestra\Testbench\Foundation\TestbenchServiceProvider',
        ]);

        $this->assertSame([
            'Orchestra\Testbench\Foundation\TestbenchServiceProvider',
        ], $config['providers']);
    }
}
