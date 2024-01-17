<?php

namespace Orchestra\Testbench\Tests\Foundation\Console;

use Orchestra\Testbench\Concerns\Database\InteractsWithSqliteDatabaseFile;
use Orchestra\Testbench\Foundation\TestbenchServiceProvider;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RequiresOperatingSystem;
use PHPUnit\Framework\Attributes\Test;

#[RequiresOperatingSystem('Linux|DAR')]
#[Group('database')]
class CreateSqliteDbCommandTest extends TestCase
{
    use InteractsWithSqliteDatabaseFile;

    /** {@inheritDoc} */
    #[\Override]
    protected function getPackageProviders($app)
    {
        return [
            TestbenchServiceProvider::class,
        ];
    }

    #[Test]
    public function it_can_generate_database_using_command()
    {
        $this->withoutSqliteDatabase(function () {
            $this->assertFalse(file_exists(database_path('database.sqlite')));

            $this->artisan('package:create-sqlite-db')
                ->expectsOutputToContain('File [database/database.sqlite] generated')
                ->assertOk();

            $this->assertTrue(file_exists(database_path('database.sqlite')));
        });
    }

    #[Test]
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
