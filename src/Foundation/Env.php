<?php

namespace Orchestra\Testbench\Foundation;

/**
 * @api
 */
class Env extends \Orchestra\Sidekick\Env
{
    /**
     * Flush the environment state.
     *
     * @return void
     */
    public static function flushState(): void
    {
        static::$putenv = true;
        static::$repository = null;
    }
}
