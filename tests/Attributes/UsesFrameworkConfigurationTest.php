<?php

namespace Orchestra\Testbench\Tests\Attributes;

use Illuminate\Foundation\Bootstrap\LoadConfiguration;
use Orchestra\Testbench\Attributes\UsesFrameworkConfiguration;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * @requires PHP >= 8.0
 */
class UsesFrameworkConfigurationTest extends TestCase
{
    /**
     * Automatically loads environment file if available.
     *
     * @var bool
     */
    protected $loadEnvironmentVariables = false;

    /** @test */
    public function it_can_load_using_testbench_configurations()
    {
        $this->assertSame('Orchestra\Testbench\Bootstrap\LoadConfiguration', \get_class($this->app[LoadConfiguration::class]));
    }

    /** @test */
    #[UsesFrameworkConfiguration]
    public function it_can_load_using_laravel_configurations()
    {
        $this->assertSame(LoadConfiguration::class, \get_class($this->app[LoadConfiguration::class]));
    }
}
