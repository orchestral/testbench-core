<?php

namespace Orchestra\Testbench\Concerns;

use Closure;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;
use PHPUnit\Runner\Version;
use PHPUnit\Util\Annotation\Registry;
use ReflectionClass;

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
        $instance = new ReflectionClass($this);

        if (! $this instanceof TestCase || $instance->isAnonymous()) {
            return;
        }

        $registry = Registry::getInstance();

        Collection::make(
            rescue(function () use ($registry) {
                return $registry->forMethod(static::class, $this->getName(false))->symbolAnnotations();
            }, [], false)
        )->filter(static function ($actions, $key) use ($name) {
            return $key === $name;
        })->each(function ($actions) use ($app) {
            Collection::make($actions ?? [])
                ->filter(function ($method) {
                    return ! \is_null($method) && method_exists($this, $method);
                })->each($callback ?? function ($method) use ($app) {
                    $this->{$method}($app);
                });
        });
    }
}
