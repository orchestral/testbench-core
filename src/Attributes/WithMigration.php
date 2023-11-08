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
     *
     * @param  array<int, string>  $types
     */
    public function __construct(...$types)
    {
        $this->types = ['laravel', ...$types];
    }
}
