<?php

namespace Orchestra\Testbench\Tests;

use Illuminate\Support\Facades\Bus;
use Orchestra\Testbench\TestCase;
use Orchestra\Testbench\Tests\Fixtures\Jobs\RegisterUser;

class EnvironmentVariablesTest extends TestCase
{

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

    /**
     * Define database migrations.
     *
     * @return void
     */
    protected function defineDatabaseMigrations()
    {
        $this->loadLaravelMigrations(['--database' => 'testing']);
    }

    /** @test */
    public function it_can_be_used_without_having_an_environment_variables_file()
    {
        $this->assertFalse(file_exists(realpath(__DIR__.'/../../laravel/.env')));
        $this->assertFalse(file_exists(base_path('./env')));
    }
}
