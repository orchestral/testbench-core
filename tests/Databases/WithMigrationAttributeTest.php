<?php

namespace Orchestra\Testbench\Tests\Databases;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\Attributes\WithMigration;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;

#[WithMigration]
class WithMigrationAttributeTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function defineEnvironment($app)
    {
        $app['config']->set('database.default', 'testing');
    }

    #[Test]
    public function it_loads_default_migrations()
    {
        $this->assertTrue(Schema::hasTable('users'));
        $this->assertTrue(Schema::hasTable('password_reset_tokens'));
        $this->assertFalse(Schema::hasTable('cache'));
        $this->assertFalse(Schema::hasTable('cache_locks'));
        $this->assertFalse(Schema::hasTable('jobs'));
        $this->assertFalse(Schema::hasTable('job_batches'));
        $this->assertTrue(Schema::hasTable('failed_jobs'));
        $this->assertFalse(Schema::hasTable('notifications'));
        $this->assertFalse(Schema::hasTable('sessions'));
    }

    #[Test]
    #[WithMigration('cache')]
    public function it_loads_caches_migrations()
    {
        $this->assertTrue(Schema::hasTable('users'));
        $this->assertTrue(Schema::hasTable('password_reset_tokens'));
        $this->assertTrue(Schema::hasTable('cache'));
        $this->assertTrue(Schema::hasTable('cache_locks'));
        $this->assertFalse(Schema::hasTable('jobs'));
        $this->assertFalse(Schema::hasTable('job_batches'));
        $this->assertTrue(Schema::hasTable('failed_jobs'));
        $this->assertFalse(Schema::hasTable('notifications'));
        $this->assertFalse(Schema::hasTable('sessions'));
    }

    #[Test]
    #[WithMigration('notifications')]
    public function it_loads_notifications_migrations()
    {
        $this->assertTrue(Schema::hasTable('users'));
        $this->assertTrue(Schema::hasTable('password_reset_tokens'));
        $this->assertFalse(Schema::hasTable('cache'));
        $this->assertFalse(Schema::hasTable('cache_locks'));
        $this->assertFalse(Schema::hasTable('jobs'));
        $this->assertFalse(Schema::hasTable('job_batches'));
        $this->assertTrue(Schema::hasTable('failed_jobs'));
        $this->assertTrue(Schema::hasTable('notifications'));
        $this->assertFalse(Schema::hasTable('sessions'));
    }

    #[Test]
    #[WithMigration('queue')]
    public function it_loads_queue_migrations()
    {
        $this->assertTrue(Schema::hasTable('users'));
        $this->assertTrue(Schema::hasTable('password_reset_tokens'));
        $this->assertFalse(Schema::hasTable('cache'));
        $this->assertFalse(Schema::hasTable('cache_locks'));
        $this->assertTrue(Schema::hasTable('jobs'));
        $this->assertTrue(Schema::hasTable('job_batches'));
        $this->assertTrue(Schema::hasTable('failed_jobs'));
        $this->assertFalse(Schema::hasTable('notifications'));
        $this->assertFalse(Schema::hasTable('sessions'));
    }

    #[Test]
    #[WithMigration('session')]
    public function it_loads_session_migrations()
    {
        $this->assertTrue(Schema::hasTable('users'));
        $this->assertTrue(Schema::hasTable('password_reset_tokens'));
        $this->assertFalse(Schema::hasTable('cache'));
        $this->assertFalse(Schema::hasTable('cache_locks'));
        $this->assertFalse(Schema::hasTable('jobs'));
        $this->assertFalse(Schema::hasTable('job_batches'));
        $this->assertTrue(Schema::hasTable('failed_jobs'));
        $this->assertFalse(Schema::hasTable('notifications'));
        $this->assertTrue(Schema::hasTable('sessions'));
    }
}
