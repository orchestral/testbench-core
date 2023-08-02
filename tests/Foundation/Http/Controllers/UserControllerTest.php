<?php

namespace Orchestra\Testbench\Tests\Foundation\Http\Controllers;

use Orchestra\Testbench\Factories\UserFactory;
use Orchestra\Testbench\Foundation\TestbenchServiceProvider;
use Orchestra\Testbench\TestCase;

/**
 * @covers \Orchestra\Testbench\Foundation\Http\Controllers\UserController
 */
class UserControllerTest extends TestCase
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
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array<int, class-string<\Illuminate\Support\ServiceProvider>>
     */
    protected function getPackageProviders($app)
    {
        return [
            TestbenchServiceProvider::class,
        ];
    }

    /**
     * @test
     */
    public function it_can_get_current_user_information()
    {
        $user = UserFactory::new()->create();

        $response = $this->actingAs($user)->get('/_testbench/user/web');

        $response->assertExactJson([
            'id' => $user->getKey(),
            'className' => \get_class($user),
        ]);
    }
}
