<?php

namespace Orchestra\Testbench\Tests\Foundation\Console;

use Orchestra\Testbench\Foundation\TestbenchServiceProvider;
use Orchestra\Testbench\TestCase;
use Orchestra\Testbench\Tests\Concerns\Database\InteractsWithSqliteDatabase;

class DropSqliteDbCommandTest extends TestCase
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
        $this->withSqliteDatabase(function () {
            $this->assertTrue(file_exists(__DIR__.'/../../../laravel/database/database.sqlite'));

            $this->artisan('package:drop-sqlite-db')
                ->assertExitCode(0);

            $this->assertFalse(file_exists(__DIR__.'/../../../laravel/database/database.sqlite'));
        });
    }
}
