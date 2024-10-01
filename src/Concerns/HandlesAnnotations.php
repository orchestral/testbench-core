<?php

namespace Orchestra\Testbench\Concerns;

use Closure;
use Illuminate\Support\Collection;

/**
 * @deprecated
 */
trait HandlesAnnotations
{
    /**
     * Parse test method annotations.
     *
     * @internal
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @param  string  $name
     */
    #[\Deprecated(since: '9.0.0')]
    protected function parseTestMethodAnnotations($app, string $name, ?Closure $callback = null): void
    {
        $this->resolvePhpUnitAnnotations()
            ->lazy()
            ->filter(static fn ($actions, string $key) => $key === $name && ! empty($actions))
            ->flatten()
            ->filter(fn ($method) => \is_string($method) && method_exists($this, $method))
            ->each($callback ?? function ($method) use ($app) {
                $this->{$method}($app);
            });
    }

    /**
     * Resolve PHPUnit method annotations.
     *
     * @phpunit-overrides
     *
     * @return \Illuminate\Support\Collection<string, mixed>
     */
    #[\Deprecated(since: '9.0.0')]
    abstract protected function resolvePhpUnitAnnotations(): Collection;
}
