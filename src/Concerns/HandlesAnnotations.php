<?php

namespace Orchestra\Testbench\Concerns;

use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;
use PHPUnit\Util\Test as TestUtil;

trait HandlesAnnotations
{
    /**
     * Parse test method annotations.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @param  string  $name
     */
    protected function parseTestMethodAnnotations($app, string $name): void
    {
        if (! $this instanceof TestCase) {
            return;
        }

        $annotations = TestUtil::parseTestMethodAnnotations(
            static::class, $this->getName(false)
        );

        Collection::make($annotations)->each(function ($location) use ($name, $app) {
            Collection::make($location[$name] ?? [])
                ->filter(function ($method) {
                    return ! \is_null($method) && \method_exists($this, $method);
                })->each(function ($method) use ($app) {
                    $this->{$method}($app);
                });
        });
    }
}
