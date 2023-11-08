<?php

namespace Orchestra\Testbench\Tests\Databases;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\Attributes\WithMigration;
use Orchestra\Testbench\TestCase;

/**
 * @requires PHP >= 8.0
 */
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

    /** @test */
    public function it_loads_the_laravel_migrations()
    {
        $this->assertTrue(Schema::hasTable('users'));
    }
}
