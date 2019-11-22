<?php

namespace Orchestra\Testbench\Tests;

use Orchestra\Testbench\TestCase;

class RequestTest extends TestCase
{
    /** @test */
    public function it_can_get_request_information()
    {
        $this->app['router']->get('hello', ['uses' => function () {
            return 'hello world';
        }]);

        $this->call('GET', 'hello?foo=bar');

        $this->assertSame('http://localhost/hello?foo=bar', url()->full());
        $this->assertSame('http://localhost/hello', url()->current());
        $this->assertSame(['foo' => 'bar'], request()->all());
    }

    /** @test */
    public function it_flashes_request_values()
    {
        $oldValue = 'test-old-value';

        $this->app['router']->get('hello', ['middleware' => 'web', 'uses' => function () use ($oldValue) {
            $request = request()->merge(['name' => $oldValue]);
            $request->flash();

            return 'hello world';
        }]);

        $this->call('GET', 'hello');

        $this->assertEquals($oldValue, old('name'));
    }
}
