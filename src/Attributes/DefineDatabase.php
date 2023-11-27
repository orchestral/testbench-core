<?php

namespace Orchestra\Testbench\Attributes;

use Attribute;
use Closure;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\RefreshDatabaseState;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final class DefineDatabase
{
    /**
     * Construct a new attribute.
     *
     * @param  string  $method
     * @param  bool  $reset
     */
    public function __construct(
        public string $method
    ) {
        //
    }

    /**
     * Handle the attribute.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @param  \Closure  $callback
     */
    public function handle(Application $app, Closure $callback): void
    {
        \call_user_func($callback, $this->method, [$app]);

        RefreshDatabaseState::$inMemoryConnections = [];
        RefreshDatabaseState::$migrated = false;
        RefreshDatabaseState::$lazilyRefreshed = false;
    }
}
