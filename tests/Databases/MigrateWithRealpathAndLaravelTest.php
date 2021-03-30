<?php

namespace Orchestra\Testbench\Tests\Databases;

use Orchestra\Testbench\TestCase;

class MigrateWithRealpathAndLaravelTest extends TestCase
{
    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
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

        // call migrations specific to our tests, e.g. to seed the db
        // the path option should be an absolute path.
        $this->loadMigrationsFrom([
            '--database' => 'testing',
            '--path' => realpath(__DIR__.'/../migrations'),
        ]);
    }

    /** @test */
    public function it_runs_the_migrations()
    {
        $users = \DB::table('testbench_users')->where('id', '=', 1)->first();

        $this->assertEquals('hello@orchestraplatform.com', $users->email);
        $this->assertTrue(\Hash::check('123', $users->password));
    }
}
