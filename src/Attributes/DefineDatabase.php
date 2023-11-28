<?php

namespace Orchestra\Testbench\Attributes;

use Attribute;
use Closure;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\RefreshDatabaseState;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final class DefineDatabase
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
     * @param  \Closure  $action
     * @return \Closure|null
     */
    public function handle(Application $app, Closure $action): ?Closure
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
