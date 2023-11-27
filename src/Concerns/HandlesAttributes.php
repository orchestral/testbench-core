<?php

namespace Orchestra\Testbench\Concerns;

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
    protected function parseTestMethodAttributes($app, string $attribute): void
    {
        $this->resolvePhpUnitAttributes()
            ->lazy()
            ->filter(static function ($attributes, string $key) use ($attribute) {
                return $key === $attribute && ! empty($attributes);
            })->flatten()
            ->each(function ($instance) use ($app) {
                $instance->handle($app, fn ($method, $parameters) => $this->{$method}(...$parameters));
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
