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
class CreateSqliteDbCommandTest extends TestCase
{
    use InteractsWithSqliteDatabaseFile;

    /** {@inheritDoc} */
    protected function defineEnvironment($app)
    {
        $this->markTestSkippedWhen(in_parallel_testing(), 'Testbench CLI uses `.env` from skeleton instead of environment variables from PHPUnit');

        parent::defineEnvironment($app);
    }

    /** {@inheritDoc} */
    protected function getPackageProviders($app)
    {
        return [
            TestbenchServiceProvider::class,
        ];
    }

    /** @test */
    public function it_can_generate_database_using_command()
    {
        $this->withoutSqliteDatabase(function () {
            $this->assertFalse(file_exists(database_path('database.sqlite')));

            $this->artisan('package:create-sqlite-db')
                ->expectsOutputToContain('File [@laravel/database/database.sqlite] generated')
                ->assertOk();

            $this->assertTrue(file_exists(database_path('database.sqlite')));
        });
    }

    /** @test */
    public function it_cannot_generate_database_using_command_when_database_already_exists()
    {
        $this->withSqliteDatabase(function () {
            $this->assertTrue(file_exists(database_path('database.sqlite')));

            $this->artisan('package:create-sqlite-db')
                ->expectsOutputToContain('File [@laravel/database/database.sqlite] already exists')
                ->assertOk();
        });
    }
}
