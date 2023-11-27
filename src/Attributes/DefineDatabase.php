<?php

namespace Orchestra\Testbench\Attributes;

use Attribute;
use Illuminate\Foundation\Testing\RefreshDatabaseState;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final class DefineDatabase
{
    /**
     * Construct a new attribute.
     *
     * @param  string  $method
     */
    public function __construct(
        public string $method,
        public bool $reset = true
    ) {
        //
    }

    /**
     * Handle the attribute.
     */
    public function after(): void
    {
        if ($this->reset === true) {
            RefreshDatabaseState::$migrated = false;
        }
    }
}
