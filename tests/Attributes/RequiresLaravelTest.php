<?php

namespace Orchestra\Testbench\Tests\Attributes;

use Orchestra\Testbench\Attributes\RequiresLaravel;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class RequiresLaravelTest extends TestCase
{
    #[Test]
    #[DataProvider('compatibleVersionDataProvider')]
    public function it_can_validate_matching_laravel_versions($version)
    {
        $stub = new RequiresLaravel($version);

        $stub->handle($this->app, function () {
            throw new \Exception;
        });

        $this->addToAssertionCount(1);
    }

    public static function compatibleVersionDataProvider()
    {
        yield ['6.0'];
        yield ['^6.0'];
        yield ['>=6.0.0'];
    }

    #[Test]
    public function it_can_invalidate_unmatched_laravel_versions()
    {
        $stub = new RequiresLaravel('<6.0.0');

        $stub->handle($this->app, function ($method, $parameters) {
            $this->assertSame('markTestSkipped', $method);
            $this->assertSame(['Requires Laravel Framework:<6.0.0'], $parameters);
        });
    }
}
