<?php

namespace Orchestra\Testbench\Tests\Integrations;

use Exception;
use Illuminate\Routing\Router;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Workbench\App\Http\Controllers\ExampleController;

class RouteTest extends TestCase
{
    /** {@inheritDoc} */
    #[\Override]
    protected function defineRoutes($router)
    {
        $router->middleware('web')->get('web/test', fn () => 'Test using web');
        $router->middleware('api')->get('api/test', fn () => 'Test using api');

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
    #[Group('phpunit-configuration')]
    public function it_can_resolve_web_group_route()
    {
        $crawler = $this->call('GET', 'web/test');

        $this->assertEquals('Test using web', $crawler->getContent());
    }

    #[Test]
    public function it_can_resolve_api_group_route()
    {
        $crawler = $this->call('GET', 'api/test');

        $this->assertEquals('Test using api', $crawler->getContent());
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
