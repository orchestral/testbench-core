<?php

namespace Orchestra\Testbench\TestCase;

use Orchestra\Testbench\TestCase;

class RequestTest extends TestCase
{
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
