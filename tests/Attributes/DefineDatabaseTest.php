<?php

namespace Orchestra\Testbench\Tests\Attributes;

use Closure;
use Illuminate\Foundation\Application;
use Mockery as m;
use Orchestra\Testbench\Attributes\DefineDatabase;
use Orchestra\Testbench\PHPUnit\TestCase;
use PHPUnit\Framework\Attributes\Test;

class DefineDatabaseTest extends TestCase
{
    #[Test]
    public function it_can_resolve_definition()
    {
        $attribute = new DefineDatabase('defineCallback', defer: false);

        $this->assertInstanceOf(DefineDatabase::class, $attribute);
        $this->assertSame('defineCallback', $attribute->method);
        $this->assertFalse($attribute->defer);
    }

    #[Test]
    public function it_can_handle_defer_definition()
    {
        $attribute = new DefineDatabase('defineCallback', defer: true);

        $this->assertInstanceOf(DefineDatabase::class, $attribute);
        $this->assertSame('defineCallback', $attribute->method);
        $this->assertTrue($attribute->defer);

        $callback = $attribute->handle($app = m::mock(Application::class), function ($method, $parameters) use ($app) {
            $this->assertSame('defineCallback', $method);
            $this->assertSame([$app], $parameters);
        });

        $this->assertInstanceOf(Closure::class, $callback);

        $callback();
    }

    #[Test]
    public function it_can_handle_eager_definition()
    {
        $attribute = new DefineDatabase('defineCallback', defer: false);

        $this->assertInstanceOf(DefineDatabase::class, $attribute);
        $this->assertSame('defineCallback', $attribute->method);
        $this->assertFalse($attribute->defer);

        $callback = $attribute->handle($app = m::mock(Application::class), function ($method, $parameters) use ($app) {
            $this->assertSame('defineCallback', $method);
            $this->assertSame([$app], $parameters);
        });

        $this->assertNull($callback);
    }
}
