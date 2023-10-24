<?php

namespace Orchestra\Testbench\Tests\Attributes;

use Orchestra\Testbench\Attributes\Define;
use Orchestra\Testbench\Attributes\DefineDatabase;
use Orchestra\Testbench\Attributes\DefineEnvironment;
use Orchestra\Testbench\Attributes\DefineRoute;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;

class DefineTest extends TestCase
{
    #[Test]
    public function it_can_resolve_environment_definition()
    {
        $attribute = (new Define('env', 'setupEnvironmentData'))->resolve();

        $this->assertInstanceOf(DefineEnvironment::class, $attribute);
        $this->assertSame('setupEnvironmentData', $attribute->method);
    }

    #[Test]
    public function it_can_resolve_database_definition()
    {
        $attribute = (new Define('db', 'setupDatabaseData'))->resolve();

        $this->assertInstanceOf(DefineDatabase::class, $attribute);
        $this->assertSame('setupDatabaseData', $attribute->method);
    }

    #[Test]
    public function it_can_resolve_route_definition()
    {
        $attribute = (new Define('route', 'setupRouteData'))->resolve();

        $this->assertInstanceOf(DefineRoute::class, $attribute);
        $this->assertSame('setupRouteData', $attribute->method);
    }

    #[Test]
    public function it_cannot_resolve_unknown_definition()
    {
        $attribute = (new Define('unknown', 'setupRouteData'))->resolve();

        $this->assertNull($attribute);
    }
}
