<?php

namespace Orchestra\Testbench\Tests;

use Illuminate\Support\Facades\Log;
use Orchestra\Testbench\TestCase;

class PhpDeprecationsTest extends TestCase
{
    /** @test */
    public function handle_php81_deprecations()
    {
        Log::shouldReceive('channel')
            ->once()->with('deprecations')
            ->andReturnSelf()
            ->shouldReceive('warning')
            ->once()
            ->withArgs(function ($message) {
                return strpos($message, 'zzz in') !== false;
            });

        trigger_error('zzz', E_USER_DEPRECATED);
    }
}
