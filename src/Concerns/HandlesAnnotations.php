<?php

namespace Orchestra\Testbench\Concerns;

use Closure;
use Illuminate\Support\Collection;

/**
 * @internal
 */
trait HandlesAnnotations
{
    /**
     * Parse test method annotations.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @param  string  $name
     */
    protected function parseTestMethodAnnotations($app, string $name, ?Closure $callback = null): void
    {
        $this->resolvePhpUnitAnnotations()
            ->filter(static function ($actions, $key) use ($name) {
                return $key === $name;
            })->each(function ($actions) use ($app, $callback) {
                Collection::make($actions ?? [])
                    ->filter(function ($method) {
                        return ! \is_null($method) && method_exists($this, $method);
                    })->each($callback ?? function ($method) use ($app) {
                        $this->{$method}($app);
                    });
            });
    }

    /**
     * Resolve PHPUnit method annotations.
     *
     * @phpunit-overrides
     *
     * @return \Illuminate\Support\Collection<string, mixed>
     */
    abstract protected function resolvePhpUnitAnnotations(): Collection;
}
