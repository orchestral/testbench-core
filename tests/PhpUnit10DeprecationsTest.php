<?php

namespace Orchestra\Testbench\Tests;

use Illuminate\Support\Facades\Log;
use Orchestra\Testbench\Attributes\DefineEnvironment;
use Orchestra\Testbench\Exceptions\DeprecatedException;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;

#[Group('deprecations')]
class PhpUnit10DeprecationsTest extends TestCase
{
    #[Test]
    public function handle_php81_deprecations_using_logs()
    {
        Log::shouldReceive('channel')
            ->once()->with('deprecations')
            ->andReturnSelf()
            ->shouldReceive('warning')
            ->once()
            ->withArgs(fn ($message) => strpos($message, 'zzz in') !== false);

        trigger_error('zzz', E_USER_DEPRECATED);
    }

    #[Test]
    #[DefineEnvironment('defineConvertDeprecationsToExceptions')]
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
