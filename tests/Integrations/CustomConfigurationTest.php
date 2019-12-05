<?php

namespace Orchestra\Testbench\Tests\Integrations;

use Orchestra\Testbench\TestCase;

class CustomConfigurationTest extends TestCase
{
    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            'Orchestra\Testbench\Tests\Stubs\Providers\CustomConfigServiceProvider',
        ];
    }

    /** @test */
    public function it_can_override_existing_configuration_on_register()
    {
        $this->assertSame('bar', config('database.redis.foo'));
    }
}
