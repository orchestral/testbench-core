<?php

namespace Orchestra\Testbench\Foundation\Console;

use Illuminate\Support\Collection;

final class TerminatingConsole
{
    /**
     * The terminating callbacks.
     *
     * @var array<int, (callable():void)>
     */
    protected static array $beforeTerminatingCallbacks = [];

    /**
     * Register a callback to be run before terminating the command.
     *
     * @param  callable():void  $callback
     * @return void
     */
    public static function before(callable $callback): void
    {
        array_unshift(self::$beforeTerminatingCallbacks, $callback);
    }

    /**
     * Register a callback to be run before terminating the command.
     *
     * @param  bool  $condition
     * @param  callable():void  $callback
     * @return void
     */
    public static function beforeWhen(bool $condition, callable $callback): void
    {
        if ($condition === true) {
            self::before($callback);
        }
    }

    /**
     * Handle terminating console.
     *
     * @return void
     */
    public static function handle(): void
    {
        Collection::make(self::$beforeTerminatingCallbacks)
            ->each(static function ($callback) {
                \call_user_func($callback);
            });

        self::purge();
    }

    /**
     * Purge terminating console callbacks.
     *
     * @return void
     */
    public static function purge(): void
    {
        self::$beforeTerminatingCallbacks = [];
    }
}
