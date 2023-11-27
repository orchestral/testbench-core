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
     */
    protected function parseTestMethodAttributes($app, string $attribute): Collection
    {
        return $this->resolvePhpUnitAttributes()
            ->filter(static function ($attributes, string $key) use ($attribute) {
                return $key === $attribute && ! empty($attributes);
            })->flatten()
            ->map(function ($instance) use ($app) {
                return $instance->handle($app, function ($method, $parameters) {
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
