<?php

namespace Orchestra\Testbench\Attributes;

use Attribute;
use Closure;
use Illuminate\Foundation\Application;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final class DefineRoute
{
    /**
     * Construct a new attribute.
     *
     * @param  string  $method
     */
    public function __construct(
        public string $method
    ) {
        //
    }

    /**
     * Handle the attribute.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @param  \Closure  $action
     */
    public function handle(Application $app, Closure $action): void
    {
        \call_user_func($action, $this->method, [$app['router']]);
    }
}
