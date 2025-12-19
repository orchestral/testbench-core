<?php

namespace Orchestra\Testbench\Concerns;

use Illuminate\Support\Str;
use ReflectionClass;

trait WithFixtures
{
    protected static function setupWithFixturesForTestingEnvironment(): void
    {
        $reflection = new ReflectionClass(static::class);

        if (! is_file($classFileName = $reflection->getFileName()) && ! str_ends_with($classFileName, '.php')) {
            return;
        }

        if (! is_file($fixtureFileName = Str::replaceLast('.php', '.fixtures.php', $classFileName))) {
            return;
        }

        require_once $fixtureFileName;
    }
}
