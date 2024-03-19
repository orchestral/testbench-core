<?php

namespace Orchestra\Testbench\Attributes;

use Attribute;
use Closure;
use Orchestra\Testbench\Contracts\Attributes\Actionable as ActionableContract;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final class DefineDatabase implements ActionableContract
{
    /**
     * The target method.
     *
     * @var string
     */
    public $method;

    /**
     * Determine if target should be deferred.
     *
     * @var bool
     */
    public $defer = true;

    /**
     * Construct a new attribute.
     *
     * @param  string  $method
     * @param  bool  $defer
     */
    public function __construct(string $method, bool $defer = true)
    {
        $this->method = $method;
        $this->defer = $defer;
    }

    /**
     * Handle the attribute.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @param  \Closure(string, array<int, mixed>):void  $action
     * @return \Closure|null
     */
    public function handle($app, Closure $action)
    {
        ResetRefreshDatabaseState::run();

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
