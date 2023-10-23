<?php

namespace Orchestra\Testbench\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final class DefineDatabase
{
    public function __construct(
        public string $method
    ) {
        //
    }
}