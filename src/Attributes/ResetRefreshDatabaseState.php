<?php

namespace Orchestra\Testbench\Attributes;

use Attribute;
use Illuminate\Foundation\Testing\RefreshDatabaseState;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
final class ResetRefreshDatabaseState
{
    /**
     * Handle the attribute.
     *
     * @param  \Illuminate\Foundation\Application  $app
     */
    public function handle($app): void
    {
        RefreshDatabaseState::$migrated = false;
        RefreshDatabaseState::$lazilyRefreshed = false;
    }
}
