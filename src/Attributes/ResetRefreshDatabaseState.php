<?php

namespace Orchestra\Testbench\Attributes;

use Attribute;
use Closure;
use Illuminate\Foundation\Testing\RefreshDatabaseState;

#[Attribute(Attribute::TARGET_METHOD)]
final class ResetRefreshDatabaseState
{
    /**
     * Handle the attribute.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @param  \Closure():void  $action
     */
    public function handle($app, Closure $action): void
    {
        RefreshDatabaseState::$inMemoryConnections = [];
        RefreshDatabaseState::$migrated = false;
        RefreshDatabaseState::$lazilyRefreshed = false;
    }
}
