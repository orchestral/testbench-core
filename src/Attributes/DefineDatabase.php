<?php

namespace Orchestra\Testbench\Attributes;

use Attribute;
use Closure;
use Illuminate\Foundation\Testing\RefreshDatabaseState;
use Orchestra\Testbench\Contracts\Attributes\Actionable as ActionableContract;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final class DefineDatabase implements ActionableContract
{
    /**
     * Construct a new attribute.
     *
     * @param  string  $method
     * @param  bool  $defer
     */
    public function __construct(
        public string $method,
        public bool $defer = true
    ) {
        //
    }

    /**
     * Handle the attribute.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @param  \Closure(string, array<int, mixed>):void  $action
     * @return \Closure|null
     */
    public function handle($app, Closure $action): ?Closure
    {
        RefreshDatabaseState::$migrated = false;
        RefreshDatabaseState::$lazilyRefreshed = false;

        $resolver = function () use ($app, $action) {
            \call_user_func($action, $this->method, [$app]);
        };

        if ($this->defer === false) {
            value($resolver);

            return null;
        }

        return $resolver;
    }
}
