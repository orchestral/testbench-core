<?php

namespace Orchestra\Testbench\Tests\Integrations;

use Exception;
use Illuminate\Routing\Router;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Workbench\App\Http\Controllers\ExampleController;

class RouteTest extends TestCase
{
    /**
     * Define routes setup.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    protected function defineRoutes($router)
    {
        $router->domain('api.localhost')
            ->group(function (Router $router) {
                $router->get('hello', fn () => 'hello from api');
            });

        $router->get('hello', ['as' => 'hi', 'uses' => fn () => 'hello world']);

        $router->get('goodbye', fn () => 'goodbye world')->name('bye');

        $router->group(['prefix' => 'boss'], function (Router $router) {
            $router->get('hello', ['as' => 'boss.hi', 'uses' => fn () => 'hello boss']);

            $router->get('goodbye', fn () => 'goodbye boss')->name('boss.bye');
        });

        $router->resource('foo', ExampleController::class);
    }

    #[Test]
    public function it_can_resolve_get_routes()
    {
        $crawler = $this->call('GET', 'hello');

        $this->assertEquals('hello world', $crawler->getContent());

        $crawler = $this->call('GET', 'goodbye');

        $this->assertEquals('goodbye world', $crawler->getContent());
    }

    #[Test]
    public function it_can_resolve_get_routes_with_prefixes()
    {
        $crawler = $this->call('GET', 'boss/hello');

        $this->assertEquals('hello boss', $crawler->getContent());

        $crawler = $this->call('GET', 'boss/goodbye');

        $this->assertEquals('goodbye boss', $crawler->getContent());
    }

    #[Test]
    public function it_can_resolve_resource_controller()
    {
        $response = $this->call('GET', 'foo');

        $response->assertStatus(200);
        $this->assertEquals('ExampleController@index', $response->getContent());
    }

    #[Test]
    public function it_can_resolve_domain_route()
    {
        $response = $this->get('http://api.localhost/hello');

        $response->assertStatus(200);
        $this->assertEquals('hello from api', $response->getContent());
    }

    #[Test]
    public function it_can_resolve_name_routes()
    {
        $this->app['router']->get('passthrough', fn () => route('bye'))->name('pass');

        $response = $this->call('GET', route('pass'));

        $response->assertStatus(200);
        $this->assertEquals('http://localhost/goodbye', $response->getContent());
    }

    #[Test]
    public function it_can_handle_route_throwing_exception()
    {
        $this->app['router']->get('bad-route', fn () => throw new Exception('Route error!'))->name('bad');

        $response = $this->call('GET', route('bad'));

        $response->assertStatus(500);
    }
}
