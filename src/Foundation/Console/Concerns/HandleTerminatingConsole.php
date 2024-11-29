<?php

namespace Orchestra\Testbench\Foundation\Console\Concerns;

use Illuminate\Support\Collection;

trait HandleTerminatingConsole
{
    /**
     * The terminating callbacks.
     *
     * @var array<int, (callable():void)>
     */
    protected $beforeTerminatingCallbacks = [];

    /**
     * Register a callback to be run before terminating the command.
     *
     * @param  callable():void  $callback
     * @return void
     */
    protected function beforeTerminating(callable $callback): void
    {
        array_unshift($this->beforeTerminatingCallbacks, $callback);
    }

    /**
     * Register a callback to be run before terminating the command.
     *
     * @param  bool  $condition
     * @param  callable():void  $callback
     * @return void
     */
    protected function beforeTerminatingWhen(bool $condition, callable $callback): void
    {
        if ($condition === true) {
            $this->beforeTerminating($callback);
        }
    }

    /**
     * Handle terminating console.
     *
     * @return void
     */
    protected function handleTerminatingConsole(): void
    {
        Collection::make($this->beforeTerminatingCallbacks)
            ->each(static function ($callback) {
                \call_user_func($callback);
            });

        $this->purgeTerminatingConsoleCallbacks();
    }

    /**
     * Purge terminating console callbacks.
     *
     * @return void
     */
    public function purgeTerminatingConsoleCallbacks(): void
    {
        $this->beforeTerminatingCallbacks = [];
    }
}
