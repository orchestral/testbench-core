<?php

namespace Orchestra\Testbench\Attributes;

use Attribute;
use Orchestra\Testbench\Contracts\Attributes\TestInvokable as TestInvokableContract;
use Orchestra\Testbench\Foundation\Env;
use Orchestra\Testbench\Foundation\UndefinedValue;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final class RequiresEnv implements TestInvokableContract
{
    /**
     * Construct a new attribute.
     *
     * @param  string  $key
     */
    public function __construct(
        public string $key
    ) {
        //
    }

    /**
     * Handle the attribute.
     *
     * @param  \PHPUnit\Framework\TestCase  $testCase
     * @return void
     */
    public function __invoke($testCase)
    {
        $value = Env::get($this->key, new UndefinedValue());

        if ($value instanceof UndefinedValue) {
            $testCase->markTestSkipped("Missing required environment variable `{$this->key}`");
        }
    }
}
