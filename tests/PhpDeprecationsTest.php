<?php

namespace Orchestra\Testbench\Tests;

use Illuminate\Support\Facades\Log;
use Orchestra\Testbench\TestCase;

/**
 * @group deprecations
 */
class PhpDeprecationsTest extends TestCase
{
    /** @test */
    public function handle_php81_deprecations_using_logs()
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

    /**
     * @test
     * @define-env defineConvertDeprecationsToExceptions
     */
    public function handle_php81_deprecations_using_exception()
    {
        $this->expectDeprecation();
        $this->expectDeprecationMessage('zzz');

        trigger_error('zzz', E_USER_DEPRECATED);
    }

    /**
     * Define environment.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function defineConvertDeprecationsToExceptions($app)
    {
        $_ENV['TESTBENCH_CONVERT_DEPRECATIONS_TO_EXCEPTIONS'] = true;

        $this->beforeApplicationDestroyed(function () {
            unset($_ENV['TESTBENCH_CONVERT_DEPRECATIONS_TO_EXCEPTIONS']);
        });
    }
}
