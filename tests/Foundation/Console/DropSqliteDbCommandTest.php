<?php

namespace Orchestra\Testbench\Tests\Foundation\Console;

use Orchestra\Testbench\Concerns\Database\InteractsWithSqliteDatabaseFile;
use Orchestra\Testbench\Foundation\TestbenchServiceProvider;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * @requires OS Linux|DAR
 *
 * @group database
 */
class DropSqliteDbCommandTest extends TestCase
{
    use InteractsWithSqliteDatabaseFile;

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

    #[Test]
    public function it_can_drop_database_using_command()
    {
        $this->withSqliteDatabase(function () {
            $this->assertTrue(file_exists(database_path('database.sqlite')));

            $this->artisan('package:drop-sqlite-db')
                ->expectsOutputToContain('File [database/database.sqlite] has been deleted')
                ->assertOk();

            $this->assertFalse(file_exists(database_path('database.sqlite')));
        });
    }

    #[Test]
    public function it_cannot_drop_database_using_command_when_database_doesnt_exists()
    {
        $this->withoutSqliteDatabase(function () {
            $this->assertFalse(file_exists(database_path('database.sqlite')));

            $this->artisan('package:drop-sqlite-db')
                ->expectsOutputToContain('File [database/database.sqlite] doesn\'t exists')
                ->assertOk();
        });
    }
}
