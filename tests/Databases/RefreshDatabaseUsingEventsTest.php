<?php

namespace Orchestra\Testbench\Tests\Databases;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Orchestra\Testbench\Attributes\WithConfig;
use Orchestra\Testbench\TestCase;

use function Orchestra\Testbench\artisan;

#[WithConfig('database.default', 'testing')]
class RefreshDatabaseUsingEventsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Perform any work that should take place once the database has finished refreshing.
     *
     * @return void
     */
    protected function afterRefreshingDatabase()
    {
        artisan($this, 'migrate', ['--database' => 'testing']);
    }

    /**
     * Destroy database migrations.
     *
     * @return void
     */
    protected function destroyDatabaseMigrations()
    {
        artisan($this, 'migrate:rollback', ['--database' => 'testing']);
    }

    /** @test */
    public function it_create_database_migrations()
    {
        $this->assertCount(1, DB::getConnections());
    }
}
