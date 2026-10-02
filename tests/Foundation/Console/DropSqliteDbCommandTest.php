<?php

namespace Orchestra\Testbench\Tests\Foundation\Console;

use Orchestra\Testbench\Concerns\Database\InteractsWithSqliteDatabaseFile;
use Orchestra\Testbench\Foundation\TestbenchServiceProvider;
use Orchestra\Testbench\Tests\TestCase;

use function Orchestra\Testbench\in_parallel_testing;

/**
 * @requires OS Linux|DAR
 *
 * @group database
 */
class DropSqliteDbCommandTest extends TestCase
{
    use InteractsWithSqliteDatabaseFile;

    /** {@inheritDoc} */
    protected function defineEnvironment($app)
    {
        $this->markTestSkippedWhen(in_parallel_testing(), 'Testbench CLI uses `.env` from skeleton instead of environment variables from PHPUnit');

        parent::defineEnvironment($app);
    }

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
        ];
    }

    /** @test */
    public function it_can_drop_database_using_command()
    {
        $this->withSqliteDatabase(function () {
            $this->assertTrue(file_exists(database_path('database.sqlite')));

            $this->artisan('package:drop-sqlite-db')
                ->expectsOutputToContain('File [@laravel/database/database.sqlite] has been deleted')
                ->assertOk();

            $this->assertFalse(file_exists(database_path('database.sqlite')));
        });
    }

    /** @test */
    public function it_cannot_drop_database_using_command_when_database_doesnt_exists()
    {
        $this->withoutSqliteDatabase(function () {
            $this->assertFalse(file_exists(database_path('database.sqlite')));

            $this->artisan('package:drop-sqlite-db')
                ->expectsOutputToContain('File [@laravel/database/database.sqlite] doesn\'t exists')
                ->assertOk();
        });
    }
}
