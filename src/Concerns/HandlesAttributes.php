<?php

namespace Orchestra\Testbench\Concerns;

use Closure;
use Illuminate\Support\Collection;

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
    protected function parseTestMethodAttributes($app, string $attribute, Closure $callback = null): Collection
    {
        return $this->resolvePhpUnitAttributes()
            ->filter(static function ($attributes, string $key) use ($attribute) {
                return $key === $attribute && ! empty($attributes);
            })->flatten()
            ->when(! \is_null($callback), function ($attributes) use ($callback) {
                if ($attributes->isNotEmpty()) {
                    value($callback, $attributes);
                }

                return $attributes;
            })
            ->map(function ($instance) use ($app) {
                return $instance->handle($app, fn ($method, $parameters) => $this->{$method}(...$parameters));
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
