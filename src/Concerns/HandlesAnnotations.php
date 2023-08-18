<?php

namespace Orchestra\Testbench\Concerns;

use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\Enumerable;
use Illuminate\Support\LazyCollection;

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
            ->filter(fn ($actions, string $key) => $key === $name && ! empty($actions))
            ->each(function (array $actions) use ($app, $callback) {
                LazyCollection::make($actions)
                    ->filter(fn ($method) => \is_string($method) && method_exists($this, $method))
                    ->each($callback ?? function ($method) use ($app) {
                        $this->{$method}($app);
                    });
            });
    }

    /**
     * Resolve PHPUnit method annotations.
     *
     * @phpunit-overrides
     *
     * @return \Illuminate\Support\Enumerable<string, mixed>
     */
    abstract protected function resolvePhpUnitAnnotations(): Enumerable;
}
