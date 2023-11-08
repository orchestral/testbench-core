<?php

namespace Orchestra\Testbench\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final class WithMigration
{
    /**
     * The target type.
     *
     * @var string
     */
    public $type;

    /**
     * Construct a new attribute.
     *
     * @param  string  $type
     */
    public function __construct(string $type = 'laravel')
    {
        $this->type = $type;
    }
}
