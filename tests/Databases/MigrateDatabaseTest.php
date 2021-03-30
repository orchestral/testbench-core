<?php

namespace Orchestra\Testbench\Tests\Databases;

use Orchestra\Testbench\TestCase;

class MigrateDatabaseTest extends TestCase
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
        $this->artisan('migrate', ['--database' => 'testing']);
    }

    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            \Orchestra\Testbench\Tests\Fixtures\Providers\ServiceProvider::class,
        ];
    }

    /** @test */
    public function it_runs_the_migrations()
    {
        $user = \DB::table('testbench_users')->where('id', '=', 1)->first();

        $this->assertEquals('hello@orchestraplatform.com', $user->email);
        $this->assertTrue(\Hash::check('123', $user->password));

        $this->assertEquals([
            'id',
            'email',
            'password',
            'created_at',
            'updated_at',
        ], \Schema::getColumnListing('testbench_users'));
    }
}
