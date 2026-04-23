<?php

namespace Orchestra\Testbench\Attributes;

use Attribute;
use Orchestra\Testbench\Contracts\Attributes\Invokable as InvokableContract;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final class WithConfig implements InvokableContract
{
    /**
     * Construct a new attribute.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @param  bool  $defer
     */
    public function __construct(
        public readonly string $key,
        public readonly mixed $value,
        public readonly bool $defer = true
    ) {}

    /**
     * Handle the attribute.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    public function __invoke($app): void
    {
        /** @var \Illuminate\Contracts\Config\Repository $config */
        $config = $app->make('config');

        $action = function () use ($config) {
            $config->set($this->key, $this->value);
        };

        if ($this->defer === true) {
            $app->booted($action);
        } else {
            value($action);
        }
    }
}
