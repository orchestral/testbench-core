<?php

namespace Orchestra\Testbench\Tests;

use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\Contracts\Config as ConfigContract;
use Orchestra\Testbench\Foundation\Config;
use Orchestra\Testbench\Foundation\Env;
use Orchestra\Testbench\Workbench\Workbench;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;

class WithWorkbenchTest extends TestCase
{
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
    public function it_can_resolve_user_model_from_workbench()
    {
        $this->assertFalse(Env::has('AUTH_MODEL'));
        $this->assertSame('Workbench\App\Models\User', config('auth.providers.users.model'));
    }

    #[Test]
    #[DataProvider('seedersDataProvider')]
    public function it_can_merge_seeders_with_illuminate_database_refresh(
        bool $seed,
        string|false $seeder,
        array|false $workbenchSeeders,
        array|false $expected
    ) {
        $stub = new MergeSeedersTestStub($seed, $seeder);

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

class MergeSeedersTestStub
{
    use WithWorkbench;

    public function __construct(protected bool $seed, protected string|false $seeders) {}

    public function __invoke(ConfigContract $config)
    {
        return $this->mergeSeedersForWorkbench($config);
    }

    public function shouldSeed(): bool
    {
        return $this->seed;
    }

    public function seeder(): string|false
    {
        return $this->seeders;
    }
}
