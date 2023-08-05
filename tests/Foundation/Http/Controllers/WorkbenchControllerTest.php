<?php

namespace Orchestra\Testbench\Tests\Foundation\Http\Controllers;

use Orchestra\Testbench\Factories\UserFactory;
use Orchestra\Testbench\Foundation\TestbenchServiceProvider;
use Orchestra\Testbench\TestCase;

/**
 * @covers \Orchestra\Testbench\Foundation\Http\Controllers\WorkbenchController
 */
class WorkbenchControllerTest extends TestCase
{
    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function defineEnvironment($app)
    {
        $app['config']->set([
            'app.key' => 'AckfSECXIvnK5r28GVIWUAxmbBSjTsmF',
            'database.default' => 'testing',
        ]);
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

        $response = $this->assertGuest('web')
            ->actingAs($user, 'web')
            ->get('/_testbench/user/web');

        $response->assertOk()->assertExactJson([
            'id' => $user->getKey(),
            'className' => \get_class($user),
        ]);
    }

    /**
     * @test
     */
    public function it_can_authenticate_a_user()
    {
        $user = UserFactory::new()->create();

        $response = $this->assertGuest('web')
            ->get("/_testbench/login/{$user->getKey()}/web");

        $response->assertNoContent(200);

        $this->assertAuthenticated('web');
    }

    /**
     * @test
     */
    public function it_can_deauthenticate_a_user()
    {
        $user = UserFactory::new()->create();

        $response = $this->assertGuest('web')
            ->actingAs($user, 'web')
            ->get('/_testbench/logout/web');

        $response->assertNoContent(200);

        $this->assertGuest('web');
    }
}
