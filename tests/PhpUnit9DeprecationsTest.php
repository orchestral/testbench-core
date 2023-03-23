<?php

namespace Orchestra\Testbench\Tests;

use ErrorException;
use Illuminate\Support\Facades\Log;
use Orchestra\Testbench\Exceptions\DeprecatedException;
use Orchestra\Testbench\TestCase;

/**
 * @group deprecations
 *
 * @requires PHPUnit < 10
 */
class PhpUnit9DeprecationsTest extends TestCase
{
    /**
     * @test
     *
     * @define-env defineConvertDeprecationsToExceptions
     */
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
     *
     * @define-env defineConvertDeprecationsToExceptions
     */
    public function handle_php81_deprecations_using_phpunit_exception()
    {
        $this->expectException(DeprecatedException::class);
        $this->expectExceptionMessage('zzz');

        trigger_error('zzz', E_USER_DEPRECATED);
    }

    /**
     * @test
     *
     * @define-env defineConvertDeprecationsToExceptions
     */
    public function handle_php81_deprecations_using_laravel_exception()
    {
        $this->expectException(DeprecatedException::class);
        $this->expectExceptionMessage('zzz');

        trigger_error('zzz', E_USER_DEPRECATED);
    }

    /**
     * @test
     *
     * @define-env defineSkippedConvertDeprecationsToExceptions
     */
    public function handle_php81_deprecations_using_laravel_exception_using_()
    {
        $this->withoutDeprecationHandling();

        $this->expectException(ErrorException::class);
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

    /**
     * Define environment.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function defineSkippedConvertDeprecationsToExceptions($app)
    {
        $value = $_ENV['TESTBENCH_CONVERT_DEPRECATIONS_TO_EXCEPTIONS'] ?? null;
        $_ENV['TESTBENCH_CONVERT_DEPRECATIONS_TO_EXCEPTIONS'] = false;

        $this->beforeApplicationDestroyed(function () use ($value) {
            if (\is_null($value)) {
                unset($_ENV['TESTBENCH_CONVERT_DEPRECATIONS_TO_EXCEPTIONS']);
            } else {
                $_ENV['TESTBENCH_CONVERT_DEPRECATIONS_TO_EXCEPTIONS'] = $value;
            }
        });
    }
}
