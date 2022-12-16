<?php

namespace Orchestra\Testbench\Tests;

use Illuminate\Support\Facades\Log;
use Orchestra\Testbench\Exceptions\DeprecatedException;
use Orchestra\Testbench\TestCase;

/**
 * @group deprecations
 */
class PhpDeprecationsTest extends TestCase
{
    /**
     * Resolve application HTTP exception handler.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function resolveApplicationExceptionHandler($app)
    {
        $app->singleton('Illuminate\Contracts\Debug\ExceptionHandler', 'Orchestra\Testbench\Tests\Exceptions\SilentConsoleHandler');
    }

    /** @test */
    public function handle_php81_deprecations_using_logs()
    {
        $this->expectException(DeprecatedException::class);

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
     */
    public function handle_php81_deprecations_using_phpunit_exception()
    {
        $this->expectException(DeprecatedException::class);
        $this->expectExceptionMessage('zzz');

        trigger_error('zzz', E_USER_DEPRECATED);
    }

    /**
     * @test
     * @define-env defineConvertDeprecationsToExceptions
     */
    public function handle_php81_deprecations_using_laravel_exception()
    {
        $this->expectException(DeprecatedException::class);
        $this->expectExceptionMessage('zzz');

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
        $value = $_ENV['TESTBENCH_CONVERT_DEPRECATIONS_TO_EXCEPTIONS'] ?? null;
        $_ENV['TESTBENCH_CONVERT_DEPRECATIONS_TO_EXCEPTIONS'] = true;

        $this->beforeApplicationDestroyed(function () use ($value) {
            if (\is_null($value)) {
                unset($_ENV['TESTBENCH_CONVERT_DEPRECATIONS_TO_EXCEPTIONS']);
            } else {
                $_ENV['TESTBENCH_CONVERT_DEPRECATIONS_TO_EXCEPTIONS'] = $value;
            }
        });
    }
}
