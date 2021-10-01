<?php

namespace Orchestra\Testbench;

use Illuminate\Testing\PendingCommand;

/**
 * Create Laravel application instance.
 *
 * @return object
 */
function container()
{
    return new class() {
        use Concerns\CreatesApplication;
    };
}

/**
 * Run artisan command.
 *
 * @param  \Orchestra\Testbench\Contracts\TestCase  $testbench
 * @param  string  $command
 * @param  array  $parameters
 *
 * @return \Illuminate\Testing\PendingCommand|int
 */
function artisan(Contracts\TestCase $testbench, string $command, array $parameters = [])
{
    return tap($testbench->artisan($command, $parameters), function ($artisan) {
        if ($artisan instanceof PendingCommand) {
            $artisan->run();
        }
    });
}
