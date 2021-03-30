<?php

namespace Orchestra\Testbench\Tests\Databases;

use Orchestra\Testbench\TestCase;

class MigrateWithRealpathTest extends TestCase
{
    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // call migrations specific to our tests, e.g. to seed the db
        // the path option should be an absolute path.
        $this->loadMigrationsFrom(realpath(__DIR__.'/../migrations'));
    }

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

    /** @test */
    public function it_runs_the_migrations()
    {
        $users = \DB::table('testbench_users')->where('id', '=', 1)->first();

        $this->assertEquals('hello@orchestraplatform.com', $users->email);
        $this->assertTrue(\Hash::check('123', $users->password));
    }
}
