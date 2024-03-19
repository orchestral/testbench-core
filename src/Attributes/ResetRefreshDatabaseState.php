<?php

namespace Orchestra\Testbench\Attributes;

use Attribute;
use Illuminate\Foundation\Testing\RefreshDatabaseState;
use Orchestra\Testbench\Contracts\Attributes\Invokable as InvokableContract;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
final class ResetRefreshDatabaseState implements InvokableContract
{
    /**
     * Handle the attribute.
     *
     * @param  \Illuminate\Foundation\Application  $app
     */
    public function __invoke($app): void
    {
        self::run();
    }

    /**
     * Execute the action.
     *
     * @return void
     */
    public static function run(): void
    {
        RefreshDatabaseState::$migrated = false;
        RefreshDatabaseState::$lazilyRefreshed = false;
    }
}
