<?php

namespace Orchestra\Testbench\Tests\Integrations;

use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;

class AggregateServiceProviderTest extends TestCase
{
    /** {@inheritDoc} */
    #[\Override]
    protected function getPackageProviders($app)
    {
        return [
            \Orchestra\Testbench\Tests\Fixtures\Providers\ParentServiceProvider::class,
        ];
    }

    #[Test]
    public function it_populate_expected_services()
    {
        $this->assertTrue($this->app->bound('parent.loaded'));
        $this->assertTrue($this->app->bound('child.loaded'));
        $this->assertTrue($this->app->bound('child.deferred.loaded'));

        $this->assertTrue($this->app->make('parent.loaded'));
        $this->assertTrue($this->app->make('child.loaded'));
        $this->assertTrue($this->app->make('child.deferred.loaded'));
    }
}
