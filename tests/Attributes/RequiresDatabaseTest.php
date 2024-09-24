<?php

namespace Orchestra\Testbench\Tests\Attributes;

use Orchestra\Testbench\Attributes\RequiresDatabase;
use Orchestra\Testbench\Attributes\WithConfig;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;

#[WithConfig('database.default', 'testing')]
class RequiresDatabaseTest extends TestCase
{
    #[Test]
    public function it_can_validate_matching_database()
    {
        $stub = new RequiresDatabase('sqlite');

        $stub->handle($this->app, function () {
            throw new \Exception;
        });

        $this->addToAssertionCount(1);

        $stub = new RequiresDatabase(['pgsql', 'sqlite']);

        $stub->handle($this->app, function () {
            throw new \Exception;
        });

        $this->addToAssertionCount(1);
    }

    #[Test]
    public function it_can_invalidate_unmatched_database()
    {
        $stub = new RequiresDatabase('mysql');

        $stub->handle($this->app, function ($method, $parameters) {
            $this->assertSame('markTestSkipped', $method);
            $this->assertSame(['Requires mysql to configured for "testing" database connection'], $parameters);
        });

        $stub = new RequiresDatabase(['mysql', 'mariadb']);

        $stub->handle($this->app, function ($method, $parameters) {
            $this->assertSame('markTestSkipped', $method);
            $this->assertSame(['Requires [mysql/mariadb] to be configured for "testing" database connection'], $parameters);
        });
    }

    #[Test]
    public function it_can_invalidate_unmatched_database_version()
    {
        $stub = new RequiresDatabase('sqlite', '<2.0.0');

        $stub->handle($this->app, function ($method, $parameters) {
            $this->assertSame('markTestSkipped', $method);
            $this->assertSame(['Requires sqlite:<2.0.0 to be configured for "testing" database connection'], $parameters);
        });
    }
}
