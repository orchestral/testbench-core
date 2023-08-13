<?php

namespace Orchestra\Testbench\Tests\Foundation\Console;

use Orchestra\Testbench\Foundation\TestbenchServiceProvider;
use Orchestra\Testbench\TestCase;
use Orchestra\Testbench\Tests\Concerns\Database\InteractsWithSqliteDatabase;

/**
 * @requires OS Linux|DAR
 *
 * @group database
 */
class CreateSqliteDbCommandTest extends TestCase
{
    use InteractsWithSqliteDatabase;

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
    public function it_can_generate_database_using_command()
    {
        $this->withoutSqliteDatabase(function () {
            $this->assertFalse(file_exists(database_path('database.sqlite')));

            $this->artisan('package:create-sqlite-db')
                ->expectsOutputToContain('Copying file [database/database.sqlite.example] to [database/database.sqlite]')
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
                ->expectsOutputToContain('File [database/database.sqlite] already exists')
                ->assertOk();
        });
    }
}
