<?php

namespace Orchestra\Testbench\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final class DefineRoute
{
    /**
     * The target method.
     *
     * @var string
     */
    public $method;

    /**
     * Construct a new attribute.
     *
     * @param  string  $method
     */
    public function __construct(string $method)
    {
        $this->method = $method;
    }
}
