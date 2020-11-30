<?php

namespace Orchestra\Testbench\Tests;

use Illuminate\Routing\Router;
use Orchestra\Testbench\TestCase;

class RouteTest extends TestCase
{
    /**
     * Define environment setup.
     *
     * @param  Illuminate\Foundation\Application  $app
     */
    protected function defineEnvironment($app)
    {
        $app['router']->domain('api.localhost')
            ->group(function (Router $router) {
                $router->get('hello', function () {
                    return 'hello from api';
                });
            });

        $app['router']->get('hello', ['as' => 'hi', 'uses' => function () {
            return 'hello world';
        }]);

        $app['router']->get('goodbye', function () {
            return 'goodbye world';
        })->name('bye');

        $app['router']->group(['prefix' => 'boss'], function (Router $router) {
            $router->get('hello', ['as' => 'boss.hi', 'uses' => function () {
                return 'hello boss';
            }]);

            $router->get('goodbye', function () {
                return 'goodbye boss';
            })->name('boss.bye');
        });

        $app['router']->resource('foo', 'Orchestra\Testbench\Tests\Stubs\Controllers\Controller');
    }

    /** @test */
    public function it_can_resolve_get_routes()
    {
        $crawler = $this->call('GET', 'hello');

        $this->assertEquals('hello world', $crawler->getContent());

        $crawler = $this->call('GET', 'goodbye');

        $this->assertEquals('goodbye world', $crawler->getContent());
    }

    /** @test */
    public function it_can_resolve_get_routes_with_prefixes()
    {
        $crawler = $this->call('GET', 'boss/hello');

        $this->assertEquals('hello boss', $crawler->getContent());

        $crawler = $this->call('GET', 'boss/goodbye');

        $this->assertEquals('goodbye boss', $crawler->getContent());
    }

    /** @test */
    public function it_can_resolve_resource_controller()
    {
        $response = $this->call('GET', 'foo');

        $response->assertStatus(200);
        $this->assertEquals('Controller@index', $response->getContent());
    }
    /** @test */
    public function it_can_resolve_domain_route()
    {
        $response = $this->get('http://api.localhost/hello');

        $response->assertStatus(200);
        $this->assertEquals('hello from api', $response->getContent());
    }

    /** @test */
    public function it_can_resolve_name_routes()
    {
        $this->app['router']->get('byebye', function () {
            return route('bye');
        })->name('bae');

        $response = $this->call('GET', route('bae'));

        $response->assertStatus(200);
        $this->assertEquals('http://localhost/goodbye', $response->getContent());
    }
}
