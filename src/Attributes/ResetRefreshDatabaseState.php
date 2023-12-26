<?php

namespace Orchestra\Testbench\Attributes;

use Attribute;
use Illuminate\Foundation\Testing\RefreshDatabaseState;
use Orchestra\Testbench\Contracts\Attributes\AfterAll as AfterAllContract;
use Orchestra\Testbench\Contracts\Attributes\BeforeAll as BeforeAllContract;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
final class ResetRefreshDatabaseState implements AfterAllContract, BeforeAllContract
{
    /**
     * Handle the attribute.
     */
    public function beforeAll(): void
    {
        self::run();
    }

    /**
     * Handle the attribute.
     */
    public function afterAll(): void
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
