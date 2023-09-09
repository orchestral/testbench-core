<?php

namespace Orchestra\Testbench\Tests\Foundation\Http\Controllers;

use Orchestra\Testbench\Contracts\Config as ConfigContract;
use Orchestra\Testbench\Factories\UserFactory;
use Orchestra\Testbench\Foundation\Config;
use Orchestra\Testbench\TestCase;
use Orchestra\Testbench\Workbench\WorkbenchServiceProvider;

/**
 * @covers \Orchestra\Testbench\Workbench\Http\Controllers\WorkbenchController
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
     * Define routes setup.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    protected function defineRoutes($router)
    {
        $router->get('/workbench', ['uses' => fn () => 'hello world']);
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
            WorkbenchServiceProvider::class,
        ];
    }

    /** @test */
    public function it_can_get_current_user_information()
    {
        $user = UserFactory::new()->create();

        $response = $this->assertGuest('web')
            ->actingAs($user, 'web')
            ->get('/_workbench/user/web');

        $response->assertOk()->assertExactJson([
            'id' => $user->getKey(),
            'className' => \get_class($user),
        ]);
    }

    /** @test */
    public function it_can_get_current_user_information_without_authenticated_user_return_empty_array()
    {
        $user = UserFactory::new()->create();

        $response = $this->assertGuest('web')
            ->get('/_workbench/user/web');

        $response->assertOk()->assertExactJson([]);
    }

    /** @test */
    public function it_can_authenticate_a_user()
    {
        $user = UserFactory::new()->create();

        $response = $this->assertGuest('web')
            ->get("/_workbench/login/{$user->getKey()}/web");

        $response->assertRedirect('/');

        $this->assertAuthenticated('web')
            ->assertAuthenticatedAs($user);
    }

    /** @test */
    public function it_can_authenticate_a_user_using_email()
    {
        $user = UserFactory::new()->create();

        $response = $this->assertGuest('web')
            ->get("/_workbench/login/{$user->email}/web");

        $response->assertRedirect('/');

        $this->assertAuthenticated('web')
            ->assertAuthenticatedAs($user);
    }

    /** @test */
    public function it_can_deauthenticate_a_user()
    {
        $user = UserFactory::new()->create();

        $response = $this->assertGuest('web')
            ->actingAs($user, 'web')
            ->get('/_workbench/logout/web');

        $response->assertRedirect('/');

        $this->assertGuest('web');
    }

    /** @test */
    public function it_can_automatically_authenticate_a_user()
    {
        $user = UserFactory::new()->create();

        $this->instance(ConfigContract::class, new Config([
            'workbench' => ['start' => '/workbench', 'user' => $user->getKey(), 'guard' => 'web'],
        ]));

        $response = $this->assertGuest('web')->get('/_workbench/');

        $response->assertRedirect('/workbench');

        $this->assertAuthenticated('web')
            ->assertAuthenticatedAs($user);
    }

    /** @test */
    public function it_can_automatically_authenticate_a_user_using_email()
    {
        $user = UserFactory::new()->create();

        $this->instance(ConfigContract::class, new Config([
            'workbench' => ['start' => '/workbench', 'user' => $user->email, 'guard' => 'web'],
        ]));

        $response = $this->assertGuest('web')->get('/_workbench/');

        $response->assertRedirect('/workbench');

        $this->assertAuthenticated('web')
            ->assertAuthenticatedAs($user);
    }

    /** @test */
    public function it_can_automatically_deauthenticate_a_user()
    {
        $user = UserFactory::new()->create();

        $this->instance(ConfigContract::class, new Config([
            'workbench' => ['start' => '/workbench', 'user' => null, 'guard' => 'web'],
        ]));

        $response = $this->assertGuest('web')
            ->actingAs($user, 'web')
            ->get('/_workbench');

        $response->assertRedirect('/workbench');

        $this->assertGuest('web');
    }
}
