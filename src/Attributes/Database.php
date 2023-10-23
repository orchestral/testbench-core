<?php

namespace Orchestra\Testbench\Attributes;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class Database
{
    public function __construct(
        public string $method
    ) {
        //
    }
}
