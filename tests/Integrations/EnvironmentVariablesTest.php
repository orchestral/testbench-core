<?php

namespace Orchestra\Testbench\Tests;

use Illuminate\Foundation\Auth\User;
use Orchestra\Testbench\Factories\UserFactory;
use Orchestra\Testbench\TestCase;

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

    /**
     * @test
     *
     * @group commander
     */
    public function it_can_be_used_without_having_an_environment_variables_file()
    {
        $user = UserFactory::new()->create();

        $this->assertFalse(file_exists(realpath(__DIR__.'/../../laravel/.env')));
        $this->assertFalse(file_exists(base_path('./env')));

        $this->assertInstanceOf(User::class, $user);
    }
}
