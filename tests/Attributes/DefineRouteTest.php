<?php

namespace Orchestra\Testbench\Tests\Attributes;

use Illuminate\Foundation\Application;
use Illuminate\Routing\Router;
use Mockery as m;
use Orchestra\Testbench\Attributes\DefineRoute;
use Orchestra\Testbench\PHPUnit\TestCase;

class DefineRouteTest extends TestCase
{
    /** @test */
    public function it_can_resolve_definition()
    {
        $attribute = new DefineRoute('defineCallback');

        $this->assertInstanceOf(DefineRoute::class, $attribute);
        $this->assertSame('defineCallback', $attribute->method);
    }

    /** @test */
    public function it_can_handle_definition()
    {
        $attribute = new DefineRoute('defineCallback');

        $this->assertInstanceOf(DefineRoute::class, $attribute);
        $this->assertSame('defineCallback', $attribute->method);

        $app = m::mock(Application::class);
        $app->shouldReceive('make')->with('router')->andReturn($router = m::mock(Router::class));

        $callback = $attribute->handle($app, function ($method, $parameters) use ($router) {
            $this->assertSame('defineCallback', $method);
            $this->assertSame([$router], $parameters);
        });

        $this->assertNull($callback);
    }
}
