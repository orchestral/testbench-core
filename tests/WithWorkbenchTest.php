<?php

namespace Orchestra\Testbench\Tests;

use Orchestra\Sidekick\Env;
use Orchestra\Testbench\Concerns\WithFixtures;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\Contracts\Config as ConfigContract;
use Orchestra\Testbench\Foundation\Config;
use Orchestra\Testbench\Workbench\Workbench;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;

class WithWorkbenchTest extends TestCase
{
    use WithFixtures;
    use WithWorkbench;

    #[Test]
    public function it_can_be_resolved()
    {
        $cachedConfig = Workbench::configuration();

        $this->assertInstanceOf(ConfigContract::class, $cachedConfig);

        $this->assertSame($cachedConfig, static::cachedConfigurationForWorkbench());

        $this->assertSame([
            'env' => ["APP_NAME='Testbench'"],
            'bootstrappers' => [],
            'providers' => ['Workbench\App\Providers\WorkbenchServiceProvider'],
            'dont-discover' => [],
        ], $cachedConfig->getExtraAttributes());
    }

    #[Test]
    public function it_can_be_manually_resolved()
    {
        $cachedConfig = static::cachedConfigurationForWorkbench();

        Workbench::flush();

        $config = static::cachedConfigurationForWorkbench();

        $this->assertInstanceOf(ConfigContract::class, $config);

        $this->assertSame($cachedConfig->toArray(), $config->toArray());
    }

    #[Test]
    #[Group('without-parallel')]
    public function it_can_auto_detect_packages_via_bootstrap_providers_file()
    {
        $loadedProviders = collect($this->app->getLoadedProviders())->keys()->all();

        $this->assertContains('Workbench\App\Providers\AppServiceProvider', $loadedProviders);
    }

    #[Test]
    #[Group('without-parallel')]
    public function it_can_resolve_user_model_from_workbench()
    {
        $this->assertFalse(Env::has('AUTH_MODEL'));
        $this->assertSame('Workbench\App\Models\User', config('auth.providers.users.model'));
    }

    #[Test]
    #[DataProvider('seedersDataProvider')]
    #[Group('without-parallel')]
    public function it_can_merge_seeders_with_illuminate_database_refresh(
        bool $seed,
        string|false $seeder,
        array|false $workbenchSeeders,
        array|false $expected
    ) {
        $stub = new WithWorkbenchTest\MergeSeedersTestStub($seed, $seeder);

        $config = new Config(['seeders' => $workbenchSeeders]);

        $this->assertSame($expected, $stub($config));
    }

    public static function seedersDataProvider()
    {
        yield [false, false, ['Workbench\Database\Seeders\DatabaseSeeder'], false];
        yield [true, false, ['Workbench\Database\Seeders\DatabaseSeeder'], ['Workbench\Database\Seeders\DatabaseSeeder']];
        yield [true, 'Database\Seeders\DatabaseSeeder', ['Workbench\Database\Seeders\DatabaseSeeder'], ['Workbench\Database\Seeders\DatabaseSeeder']];
        yield [false, 'Database\Seeders\DatabaseSeeder', ['Workbench\Database\Seeders\DatabaseSeeder'], false];
        yield [true, 'Database\Seeders\DatabaseSeeder', ['Database\Seeders\DatabaseSeeder', 'Workbench\Database\Seeders\DatabaseSeeder'], ['Workbench\Database\Seeders\DatabaseSeeder']];
        yield [true, 'Workbench\Database\Seeders\DatabaseSeeder', ['Workbench\Database\Seeders\DatabaseSeeder'], false];
    }
}
