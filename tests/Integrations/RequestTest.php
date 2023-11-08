<?php

namespace Orchestra\Testbench\Tests\Integrations;

use Orchestra\Testbench\TestCase;

class RequestTest extends TestCase
{
    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function defineEnvironment($app)
    {
        $app['config']->set(['app.key' => 'AckfSECXIvnK5r28GVIWUAxmbBSjTsmF']);
    }

    /**
     * Define routes setup.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    protected function defineRoutes($router)
    {
        $router->get('hello', ['uses' => fn () => 'hello world']);
    }

    /**
     * Define web routes setup.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    protected function defineWebRoutes($router)
    {
        $router->get('web/hello', ['middleware' => 'web', 'uses' => function () {
            $request = request()->merge(['name' => 'test-old-value']);
            $request->flash();

            return 'hello world';
        }]);
    }

    /** @test */
    public function it_can_get_request_information()
    {
        $this->call('GET', 'hello?foo=bar');

        $this->assertSame('http://localhost/hello?foo=bar', url()->full());
        $this->assertSame('http://localhost/hello', url()->current());
        $this->assertSame(['foo' => 'bar'], request()->all());
    }

    /**
     * @test
     *
     * @group session
     */
    public function it_flashes_request_values()
    {
        $this->call('GET', 'web/hello');

        $this->assertEquals('test-old-value', old('name'));
    }
}
