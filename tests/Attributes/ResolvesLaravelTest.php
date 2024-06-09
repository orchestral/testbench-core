<?php

namespace Orchestra\Testbench\Tests\Attributes;

use Illuminate\Foundation\Bootstrap\LoadConfiguration;
use Orchestra\Testbench\Attributes\ResolvesLaravel;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ResolvesLaravelTest extends TestCase
{
    #[Test]
    #[ResolvesLaravel('laravelDefaultConfiguration')]
    public function it_can_resolve_defined_configuration()
    {
        $this->assertSame(LoadConfiguration::class, \get_class($this->app[LoadConfiguration::class]));
    }

    /**
     * Resolve Laravel.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    public function laravelDefaultConfiguration($app)
    {
        $app->bind(LoadConfiguration::class, LoadConfiguration::class);
    }
}
