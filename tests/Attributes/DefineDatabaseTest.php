<?php

namespace Orchestra\Testbench\Tests\Attributes;

use Closure;
use Illuminate\Foundation\Application;
use Mockery as m;
use Orchestra\Testbench\Attributes\DefineDatabase;
use PHPUnit\Framework\TestCase;

class DefineDatabaseTest extends TestCase
{
    /**
     * Teardown the test environment.
     */
    protected function tearDown(): void
    {
        m::close();
    }

    /** @test */
    public function it_can_resolve_definition()
    {
        $attribute = new DefineDatabase('setupDatabaseData', false);

        $this->assertInstanceOf(DefineDatabase::class, $attribute);
        $this->assertSame('setupDatabaseData', $attribute->method);
        $this->assertFalse($attribute->defer);
    }

    /** @test */
    public function it_can_handle_defer_definition()
    {
        $attribute = new DefineDatabase('setupDatabaseData', true);

        $this->assertInstanceOf(DefineDatabase::class, $attribute);
        $this->assertSame('setupDatabaseData', $attribute->method);
        $this->assertTrue($attribute->defer);

        $callback = $attribute->handle($app = m::mock(Application::class), function ($method, $parameters) use ($app) {
            $this->assertSame('setupDatabaseData', $method);
            $this->assertSame([$app], $parameters);
        });

        $this->assertInstanceOf(Closure::class, $callback);

        $callback();
    }

    /** @test */
    public function it_can_handle_eager_definition()
    {
        $attribute = new DefineDatabase('setupDatabaseData', false);

        $this->assertInstanceOf(DefineDatabase::class, $attribute);
        $this->assertSame('setupDatabaseData', $attribute->method);
        $this->assertFalse($attribute->defer);

        $callback = $attribute->handle($app = m::mock(Application::class), function ($method, $parameters) use ($app) {
            $this->assertSame('setupDatabaseData', $method);
            $this->assertSame([$app], $parameters);
        });

        $this->assertNull($callback);
    }
}
