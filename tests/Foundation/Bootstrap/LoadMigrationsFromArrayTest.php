<?php

namespace Orchestra\Testbench\Tests\Foundation\Bootstrap;

use Illuminate\Database\Events\DatabaseRefreshed;
use Illuminate\Database\Migrations\Migrator;
use Mockery as m;
use Orchestra\Testbench\Foundation\Bootstrap\LoadMigrationsFromArray;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;

class LoadMigrationsFromArrayTest extends TestCase
{
    #[Test]
    public function it_can_register_migrations()
    {
        $this->instance('migrator', $migrator = m::mock(Migrator::class));

        $paths = [__DIR__.'/../../migrations'];

        $migrator->shouldReceive('path')->once()->with($paths[0])->andReturnNull()
            ->shouldReceive('path')->once()->with($this->app->basePath('migrations'))->andReturnNull();

        (new LoadMigrationsFromArray($paths))->bootstrap($this->app);
    }

    #[Test]
    public function it_can_skip_migrations_registration()
    {
        $this->instance('migrator', $migrator = m::mock(Migrator::class));

        $migrator->shouldReceive('path')->never();

        (new LoadMigrationsFromArray(false))->bootstrap($this->app);
    }

    #[Test]
    public function it_can_seed_database_after_refreshed()
    {
        (new LoadMigrationsFromArray(false, [
            'seeders' => ['\TestbenchDatabaseSeeder'],
        ]))->bootstrap($this->app);

        $this->instance('\TestbenchDatabaseSeeder', $seeder = m::mock('TestbenchDatabaseSeeder'));

        $seeder->shouldReceive('setContainer')->once()->with($this->app)->andReturnSelf()
            ->shouldReceive('setCommand')->once()->andReturnSelf()
            ->shouldReceive('__invoke')->once()->andReturnNull();

        app('events')->dispatch(new DatabaseRefreshed());
    }

    #[Test]
    public function it_can_skip_database_seeding_after_refreshed()
    {
        (new LoadMigrationsFromArray(false, false))->bootstrap($this->app);

        app('events')->dispatch(new DatabaseRefreshed());
    }
}
