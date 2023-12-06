<?php

namespace Orchestra\Testbench\Tests\Foundation;

use Orchestra\Testbench\Foundation\Config;
use Orchestra\Testbench\Foundation\TestbenchServiceProvider;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    /** @test */
    public function it_can_load_configuration_file()
    {
        $config = Config::loadFromYaml(__DIR__.'/stubs/');

        $this->assertNull($config['laravel']);
        $this->assertSame(['APP_DEBUG=(false)'], $config['env']);
        $this->assertSame([], $config['bootstrappers']);
        $this->assertSame([TestbenchServiceProvider::class], $config['providers']);
        $this->assertSame([], $config['dont-discover']);
        $this->assertSame([], $config['migrations']);
        $this->assertFalse($config['seeders']);

        $this->assertSame([
            'env' => [
                'APP_DEBUG=(false)',
            ],
            'bootstrappers' => [],
            'providers' => [
                TestbenchServiceProvider::class,
            ],
            'dont-discover' => [],
        ], $config->getExtraAttributes());

        $this->assertSame([
            'start' => '/workbench',
            'user' => 'crynobone@gmail.com',
            'guard' => null,
            'install' => true,
            'welcome' => null,
            'sync' => [],
            'build' => [],
            'assets' => [],
            'discovers' => [
                'config' => false,
                'web' => false,
                'api' => false,
                'commands' => false,
                'components' => false,
                'views' => false,
            ],
        ], $config->getWorkbenchAttributes());

        $this->assertSame([
            'config' => false,
            'web' => false,
            'api' => false,
            'commands' => false,
            'components' => false,
            'views' => false,
        ], $config->getWorkbenchDiscoversAttributes());
    }

    /** @test */
    public function it_can_load_default_configuration()
    {
        $config = new Config();

        $this->assertNull($config['laravel']);
        $this->assertSame([], $config['env']);
        $this->assertSame([], $config['bootstrappers']);
        $this->assertSame([], $config['providers']);
        $this->assertSame([], $config['dont-discover']);
        $this->assertSame([], $config['migrations']);
        $this->assertFalse($config['seeders']);

        $this->assertSame([
            'env' => [],
            'bootstrappers' => [],
            'providers' => [],
            'dont-discover' => [],
        ], $config->getExtraAttributes());

        $this->assertSame([
            'start' => '/',
            'user' => null,
            'guard' => null,
            'install' => true,
            'welcome' => null,
            'sync' => [],
            'build' => [],
            'assets' => [],
            'discovers' => [
                'config' => false,
                'web' => false,
                'api' => false,
                'commands' => false,
                'components' => false,
                'views' => false,
            ],
        ], $config->getWorkbenchAttributes());

        $this->assertSame([
            'config' => false,
            'web' => false,
            'api' => false,
            'commands' => false,
            'components' => false,
            'views' => false,
        ], $config->getWorkbenchDiscoversAttributes());
    }

    /** @test */
    public function it_can_add_additional_providers_to_configuration_file()
    {
        $config = Config::loadFromYaml(__DIR__.'/stubs/');

        $this->assertSame([
            TestbenchServiceProvider::class,
        ], $config['providers']);

        $config->addProviders([
            \Orchestra\Testbench\Tests\Fixtures\Providers\ChildServiceProvider::class,
        ]);

        $this->assertSame([
            TestbenchServiceProvider::class,
            \Orchestra\Testbench\Tests\Fixtures\Providers\ChildServiceProvider::class,
        ], $config['providers']);
    }

    /** @test */
    public function it_cant_add_duplicated_providers_to_configuration_file()
    {
        $config = Config::loadFromYaml(__DIR__.'/stubs/');

        $this->assertSame([
            TestbenchServiceProvider::class,
        ], $config['providers']);

        $config->addProviders([
            TestbenchServiceProvider::class,
        ]);

        $this->assertSame([
            TestbenchServiceProvider::class,
        ], $config['providers']);
    }
}
