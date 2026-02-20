<?php

namespace Orchestra\Testbench\Concerns;

use Illuminate\Support\Str;

use function Orchestra\Sidekick\Filesystem\filename_from_classname;

/**
 * @api
 *
 * @codeCoverageIgnore
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
        $classFileName = filename_from_classname(static::class);

        if ($classFileName === false) {
            return;
        }

        if (! is_file($fixtureFileName = Str::replaceLast('.php', '.fixtures.php', $classFileName))) {
            return;
        }

        require $fixtureFileName;
    }
}
