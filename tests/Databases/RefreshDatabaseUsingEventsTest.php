<?php

namespace Orchestra\Testbench\Tests\Databases;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\Attributes\ResetRefreshDatabaseState;
use Orchestra\Testbench\Attributes\WithConfig;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;

#[ResetRefreshDatabaseState]
#[WithConfig('database.default', 'testing')]
class RefreshDatabaseUsingEventsTest extends TestCase
{
    use RefreshDatabase, WithWorkbench;

    /**
     * Perform any work that should take place once the database has finished refreshing.
     *
     * @return void
     */
    protected function afterRefreshingDatabase()
    {
        Schema::create('testbench_staffs', function ($table) {
            $table->increments('id');
            $table->string('email');
            $table->string('password');

            $table->timestamps();
        });
    }

    /**
     * Destroy database migrations.
     *
     * @return void
     */
    protected function destroyDatabaseMigrations()
    {
        Schema::dropIfExists('testbench_staffs');
    }

    #[Test]
    public function it_create_database_migrations()
    {
        $this->assertEquals([
            'id',
            'email',
            'password',
            'created_at',
            'updated_at',
        ], Schema::getColumnListing('testbench_staffs'));
    }
}
