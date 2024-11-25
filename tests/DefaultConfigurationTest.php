<?php

namespace Orchestra\Testbench\Tests;

use Carbon\CarbonInterface;
use DateTimeImmutable;
use DateTimeInterface;
use Illuminate\Foundation\Auth\User;
use Illuminate\Foundation\Bootstrap\LoadConfiguration;
use Illuminate\Support\Facades\Date;
use Orchestra\Testbench\Attributes\WithConfig;
use Orchestra\Testbench\Foundation\Env;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;

#[WithConfig('app.key', 'AckfSECXIvnK5r28GVIWUAxmbBSjTsmF')]
class DefaultConfigurationTest extends TestCase
{
    #[Test]
    public function it_can_load_using_testbench_configurations()
    {
        $this->assertSame('Orchestra\Testbench\Bootstrap\LoadConfiguration', \get_class($this->app[LoadConfiguration::class]));
    }

    #[Test]
    public function it_populate_expected_debug_config()
    {
        $this->assertSame((Env::has('TESTBENCH_PACKAGE_TESTER') ? true : false), $this->app['config']['app.debug']);
    }

    #[Test]
    #[Group('phpunit-configuration')]
    public function it_populate_expected_app_key_config()
    {
        $this->assertSame('AckfSECXIvnK5r28GVIWUAxmbBSjTsmF', $this->app['config']['app.key']);
    }

    #[Test]
    public function it_populate_expected_testing_config()
    {
        tap($this->app['config']['database.connections.testing'], function ($config) {
            $this->assertTrue(isset($config));
            $this->assertEquals([
                'driver' => 'sqlite',
                'database' => ':memory:',
                'foreign_key_constraints' => false,
            ], $config);
        });

        $this->assertTrue($this->usesSqliteInMemoryDatabaseConnection('testing'));
        $this->assertFalse($this->usesSqliteInMemoryDatabaseConnection('sqlite'));
    }

    #[Test]
    public function it_populate_expected_cache_defaults()
    {
        $this->assertEquals(Env::has('TESTBENCH_PACKAGE_TESTER') ? 'database' : 'array', $this->app['config']['cache.default']);
    }

    #[Test]
    public function it_populate_expected_session_defaults()
    {
        $this->assertEquals(Env::has('TESTBENCH_PACKAGE_TESTER') ? 'cookie' : 'array', $this->app['config']['session.driver']);
    }

    #[Test]
    public function it_uses_mutable_dates_by_default()
    {
        $date = Date::parse('2023-01-01');

        $this->assertInstanceOf(CarbonInterface::class, $date);
        $this->assertInstanceOf(DateTimeInterface::class, $date);
        $this->assertNotInstanceOf(DateTimeImmutable::class, $date);
    }

    #[Test]
    public function it_resolve_the_default_user_model()
    {
        $this->assertSame(User::class, $this->app['config']['auth.providers.users.model']);
    }
}
