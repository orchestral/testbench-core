<?php

namespace Orchestra\Testbench\Attributes;

use Attribute;
use Orchestra\Testbench\Contracts\Attributes\Invokable as InvokableContract;
use Orchestra\Testbench\Foundation\Env;
use Orchestra\Testbench\Foundation\UndefinedValue;

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
     * @var string|null
     */
    public $value;

    /**
     * Construct a new attribute.
     *
     * @param  string  $key
     * @param  string|null  $value
     */
    public function __construct(string $key, ?string $value)
    {
        $this->key = $key;
        $this->value = $value;
    }

    /**
     * Handle the attribute.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return \Closure|null
     */
    public function __invoke($app)
    {
        $value = Env::get($this->key, new UndefinedValue());

        Env::set($this->key, $this->value ?? '(null)');

        return function () use ($value) {
            if ($value instanceof UndefinedValue) {
                Env::forget($this->key);
            } else {
                Env::set($this->key, Env::encode($value));
            }
        };
    }
}
