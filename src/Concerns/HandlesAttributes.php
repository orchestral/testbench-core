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
    protected function parseTestMethodAttributes($app, string $attribute, ?Closure $callback = null): void
    {
        $this->resolvePhpUnitAttributes()
            ->lazy()
            ->filter(static function ($attributes, string $key) use ($attribute) {
                return $key === $attribute && ! empty($attributes);
            })->flatten()
            ->when(
                \is_null($callback),
                function ($attributes) use ($app) {
                    $attributes->filter(fn ($instance) => \is_string($instance->method) && method_exists($this, $instance->method))
                        ->each(function ($instance) use ($app) {
                            $this->{$instance->method}($app);
                        });
                },
                static function ($attributes) use ($callback) {
                    $attributes->each($callback);
                }
            );
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
