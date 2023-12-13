<?php

namespace Orchestra\Testbench\Attributes;

use Attribute;
use Closure;
use Orchestra\Testbench\Contracts\Attributes\Invokable as InvokableContract;
use Orchestra\Testbench\Foundation\Env;
use Orchestra\Testbench\Foundation\UndefinedValue;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final class WithEnv implements InvokableContract
{
    /**
     * Construct a new attribute.
     *
     * @param  string  $key
     * @param  string|null  $value
     */
    public function __construct(
        public string $key,
        public ?string $value
    ) {
        //
    }

    /**
     * Handle the attribute.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return \Closure|null
     */
    public function __invoke($app): ?Closure
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
