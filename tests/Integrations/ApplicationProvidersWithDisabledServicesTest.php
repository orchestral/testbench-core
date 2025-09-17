<?php

namespace Orchestra\Testbench\Tests\Integrations;

use Illuminate\View\ViewServiceProvider;
use Orchestra\Testbench\Tests\TestCase;

class ApplicationProvidersWithDisabledServicesTest extends TestCase
{
    /** {@inheritDoc} */
    #[\Override]
    protected function overrideApplicationProviders($app)
    {
        return [ViewServiceProvider::class => false];
    }

    /** @test */
    public function it_does_not_loads_the_default_services()
    {
        $this->assertFalse($this->app->bound('blade.compiler'));
        $this->assertFalse($this->app->resolved('blade.compiler'));
    }
}
