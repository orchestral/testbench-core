<?php

namespace Orchestra\Testbench;

use Illuminate\Testing\PendingCommand;

function artisan(Contracts\TestCase $testbench, string $command, array $parameters = [])
{
    return tap($testbench->artisan($command, $parameters), function ($artisan) {
        if ($artisan instanceof PendingCommand) {
            $artisan->run();
        }
    });
}
