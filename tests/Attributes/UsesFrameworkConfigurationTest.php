<?php

namespace Orchestra\Testbench\Tests\Attributes;

use Illuminate\Foundation\Bootstrap\LoadConfiguration;
use Orchestra\Testbench\Attributes\UsesFrameworkConfiguration;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;

class UsesFrameworkConfigurationTest extends TestCase
{
    /**
     * Automatically loads environment file if available.
     *
     * @var bool
     */
    protected $loadEnvironmentVariables = false;

    #[Test]
    public function it_can_load_using_testbench_configurations()
    {
        $this->assertSame('Orchestra\Testbench\Bootstrap\LoadConfiguration', \get_class($this->app[LoadConfiguration::class]));

        $this->assertSame('testing', config('app.env'));
        $this->assertSame('Illuminate\Foundation\Auth\User', config('auth.providers.users.model'));
    }

    #[Test]
    #[UsesFrameworkConfiguration]
    public function it_can_load_using_laravel_configurations()
    {
        $this->assertSame(LoadConfiguration::class, \get_class($this->app[LoadConfiguration::class]));

        $this->assertSame('testing', config('app.env'));
        $this->assertSame('App\Models\User', config('auth.providers.users.model'));
    }
}
