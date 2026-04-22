<?php

namespace Orchestra\Testbench\Attributes;

use Attribute;
use Orchestra\Testbench\Contracts\Attributes\Invokable as InvokableContract;
use Illuminate\Contracts\Config\Repository;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final class WithConfig implements InvokableContract
{
    /**
     * Construct a new attribute.
     *
     * @param  string  $key
     * @param  mixed  $value
     */
    public function __construct(
        public readonly string $key,
        public readonly mixed $value
    ) {}

    /**
     * Handle the attribute.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    public function __invoke($app): void
    {
        // This ensures all configuration keys are loaded before adding the values.
        $app->afterResolving('config', function (Repository $config) {
            $config->set($this->key, $this->value);
        });
    }
}
