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
    protected function parseTestMethodAttributes($app, string $attribute, Closure $callback = null): void
    {
        $callback = $callback ?? fn ($instance) => $this->{$instance->method}($app);

        $this->resolvePhpUnitAttributes()
            ->lazy()
            ->filter(static function ($attributes, string $key) use ($attribute) {
                return $key === $attribute && ! empty($attributes);
            })->flatten()
            ->each(function ($instance) use ($callback) {
                if (method_exists($instance, 'before')) {
                    $instance->before();
                }

                value($callback, $instance);

                if (method_exists($instance, 'after')) {
                    $instance->after();
                }
            });
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
