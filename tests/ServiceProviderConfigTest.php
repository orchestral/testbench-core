<?php

namespace Orchestra\Testbench\Tests;

use Orchestra\Testbench\TestCase;
use Orchestra\Testbench\Tests\Stubs\Providers\ConfigurableServiceProvider;

class ServiceProviderConfigTest extends TestCase
{
    /** @test */
    public function it_can_resolve_configuration_before_a_service_provider_is_run()
    {
        $this->assertEquals('test', ConfigurableServiceProvider::$configValue);
    }

    protected function resolveServiceProviderConfiguration($app)
    {
        $app['config']->set('test', 'test');
    }

    protected function getPackageProviders($app)
    {
        return [
            ConfigurableServiceProvider::class,
        ];
    }
}
