<?php

namespace Orchestra\Testbench\Concerns;

use Illuminate\Support\Str;
use ReflectionClass;

/**
 * @api
 */
trait WithFixtures
{
    /**
     * Setup test case to include fixture file using ".fixtures.php" suffix if it's available.
     *
     * @return void
     */
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
