<?php

namespace Orchestra\Testbench\Tests\Integrations;

use Illuminate\Foundation\Bootstrap\LoadConfiguration;
use Orchestra\Testbench\Attributes\ResolvesLaravel;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;

use function Orchestra\Testbench\package_path;

class LoadUsingFrameworkConfigurationTest extends TestCase
{
    protected function overrideLaravelConfiguration($app)
    {
        $app->instance(LoadConfiguration::class, new LoadConfiguration());

        $app->useConfigPath(package_path(['vendor', 'laravel', 'framework', 'config-stubs']));
    }

    #[Test]
    #[ResolvesLaravel('overrideLaravelConfiguration')]
    public function it_can_load_using_laravel_configurations()
    {
        $this->assertSame(LoadConfiguration::class, \get_class($this->app[LoadConfiguration::class]));

        $this->assertSame('testing', config('app.env'));
        $this->assertSame('App\Models\User', config('auth.providers.users.model'));
    }
}
