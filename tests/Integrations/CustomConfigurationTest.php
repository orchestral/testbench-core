<?php

namespace Orchestra\Testbench\Tests\Integrations;

use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;

class CustomConfigurationTest extends TestCase
{
    /** {@inheritDoc} */
    #[\Override]
    protected function getPackageProviders($app)
    {
        return [
            \Orchestra\Testbench\Tests\Fixtures\Providers\CustomConfigServiceProvider::class,
        ];
    }

    #[Test]
    public function it_can_override_existing_configuration_on_register()
    {
        $this->assertSame('bar', config('database.redis.foo'));
    }
}
