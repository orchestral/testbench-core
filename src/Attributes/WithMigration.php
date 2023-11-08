<?php

namespace Orchestra\Testbench\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final class WithMigration
{
    /**
     * The target types.
     *
     * @var array
     */
    public $types = [];

    /**
     * Construct a new attribute.
     */
    public function __construct()
    {
        $this->types = array_merge(['laravel'], func_get_args());
    }
}
