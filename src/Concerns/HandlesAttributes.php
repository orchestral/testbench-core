<?php

namespace Orchestra\Testbench\Concerns;

use Illuminate\Support\Collection;
use Orchestra\Testbench\Contracts\Attributes\Actionable as ActionableContract;
use Orchestra\Testbench\Contracts\Attributes\Invokable as InvokableContract;

/**
 * @internal
 */
trait HandlesAttributes
{
    /**
     * Parse test method attributes.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @param  class-string  $attribute
     * @return \Illuminate\Support\Collection<int, (\Closure():void)>
     */
    protected function parseTestMethodAttributes($app, string $attribute): Collection
    {
        return $this->resolvePhpUnitAttributes()
            ->filter(static function ($attributes, string $key) use ($attribute) {
                return $key === $attribute && ! empty($attributes);
            })->flatten()
            ->map(function ($instance) use ($app) {
                if ($instance instanceof InvokableContract) {
                    return $instance($app);
                } elseif ($instance instanceof ActionableContract) {
                    return $instance->handle($app, fn ($method, $parameters) => $this->{$method}(...$parameters));
                }
            })->filter()
            ->values();
    }

    /**
     * Resolve PHPUnit method attributes.
     *
     * @phpunit-overrides
     *
     * @return \Illuminate\Support\Collection<string, mixed>
     */
    abstract protected function resolvePhpUnitAttributes(): Collection;
}
