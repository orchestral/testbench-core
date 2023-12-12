<?php

namespace Orchestra\Testbench\Attributes;

use Attribute;
use Orchestra\Testbench\Contracts\Attributes\Invokable as InvokableContract;
use Orchestra\Testbench\Foundation\Env;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final class WithEnv implements InvokableContract
{
    /**
     * The target environment key.
     *
     * @var string
     */
    public $key;

    /**
     * The target environment value.
     *
     * @var string
     */
    public $value;

    /**
     * Construct a new attribute.
     *
     * @param  string  $key
     * @param  string  $value
     */
    public function __construct(string $key, string $value)
    {
        $this->key = $key;
        $this->value = $value;
    }

    /**
     * Handle the attribute.
     *
     * @param  \Illuminate\Foundation\Application  $app
     */
    public function __invoke($app): void
    {
        Env::set($this->key, $this->value);
    }
}
