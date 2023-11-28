<?php

namespace Orchestra\Testbench\Concerns;

use Illuminate\Support\Collection;
use Orchestra\Testbench\Contracts\Attributes\Actionable as ActionableContract;

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
                return ! $instance instanceof ActionableContract
                    ? $instance->handle($app)
                    : $instance->handle($app, function ($method, $parameters) {
                        $this->{$method}(...$parameters);
                    });
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
