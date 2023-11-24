<?php

namespace Orchestra\Testbench\Attributes;

#[\Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final class DefineEnvironment
{
    /**
     * Construct a new attribute.
     *
     * @param  string  $method
     */
    public function __construct(
        public string $method
    ) {
        //
    }
}
