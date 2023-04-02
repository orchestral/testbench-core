<?php

namespace Orchestra\Testbench\Tests\Foundation\Bootstrap;

use Illuminate\Database\Migrations\Migrator;
use Mockery as m;
use Orchestra\Testbench\Foundation\Bootstrap\LoadMigrationsFromArray;
use Orchestra\Testbench\TestCase;

class LoadMigrationsFromArrayTest extends TestCase
{
    /** @test */
    public function test_it_can_register_migrations()
    {
        $this->instance('migrator', $migrator = m::mock(Migrator::class));

        $paths = [__DIR__.'/../../migrations'];

        $migrator->shouldReceive('path')->once()->with($paths[0])->andReturnNull()
            ->shouldReceive('path')->once()->with($this->app->basePath('migrations'))->andReturnNull();

        (new LoadMigrationsFromArray($paths))->bootstrap($this->app);
    }

    /** @test */
    public function test_it_can_skip_migrations_registration()
    {
        $this->instance('migrator', $migrator = m::mock(Migrator::class));

        $migrator->shouldReceive('path')->never();

        (new LoadMigrationsFromArray(false))->bootstrap($this->app);
    }
}
