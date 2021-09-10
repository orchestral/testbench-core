<?php

namespace Orchestra\Testbench;

use Illuminate\Testing\PendingCommand;
use Orchestra\Testbench\Contracts\TestCase;

function artisan(TestCase $testbench, string $command, array $parameters = [])
{
    return tap($testbench->artisan($command, $parameters), function ($artisan) {
        if ($artisan instanceof PendingCommand) {
            $artisan->run();
        }
    });
}
