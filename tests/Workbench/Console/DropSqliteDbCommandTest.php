<?php

namespace Orchestra\Testbench\Tests\Workbench\Console;

use Orchestra\Testbench\Concerns\Database\InteractsWithSqliteDatabaseFile;
use Orchestra\Testbench\Foundation\TestbenchServiceProvider;
use Orchestra\Testbench\TestCase;
use Orchestra\Testbench\Workbench\WorkbenchServiceProvider;

/**
 * @requires OS Linux|DAR
 *
 * @group database
 */
class DropSqliteDbCommandTest extends TestCase
{
    use InteractsWithSqliteDatabaseFile;

    protected $namespace = 'workbench';

    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array<int, class-string<\Illuminate\Support\ServiceProvider>>
     */
    protected function getPackageProviders($app)
    {
        return [
            TestbenchServiceProvider::class,
            WorkbenchServiceProvider::class,
        ];
    }

    /** @test */
    public function it_can_drop_database_using_command()
    {
        $this->withSqliteDatabase(function () {
            $this->assertTrue(file_exists(__DIR__.'/../../../laravel/database/database.sqlite'));

            $this->artisan("{$this->namespace}:drop-sqlite-db")
                ->expectsOutputToContain('File [database/database.sqlite] has been deleted')
                ->assertOk();

            $this->assertFalse(file_exists(__DIR__.'/../../../laravel/database/database.sqlite'));
        });
    }

    /** @test */
    public function it_cannot_drop_database_using_command_when_database_doesnt_exists()
    {
        $this->withoutSqliteDatabase(function () {
            $this->assertFalse(file_exists(__DIR__.'/../../../laravel/database/database.sqlite'));

            $this->artisan("{$this->namespace}:drop-sqlite-db")
                ->expectsOutputToContain('File [database/database.sqlite] does not exists')
                ->assertOk();
        });
    }
}
