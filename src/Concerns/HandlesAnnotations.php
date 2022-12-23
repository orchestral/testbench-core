<?php

namespace Orchestra\Testbench\Concerns;

use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;
use PHPUnit\Runner\Version;
use ReflectionClass;

trait HandlesAnnotations
{
    /**
     * Parse test method annotations.
     *
     * @phpunit-overrides
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @param  string  $name
     */
    protected function parseTestMethodAnnotations($app, string $name): void
    {
        $instance = new ReflectionClass($this);

        if (! $this instanceof TestCase || $instance->isAnonymous()) {
            return;
        }

        if (class_exists(Version::class) && version_compare(Version::id(), '10', '>=')) {
            /** @phpstan-ignore-next-line */
            $registry = \PHPUnit\Metadata\Annotation\Parser\Registry::getInstance();
        } else {
            /** @phpstan-ignore-next-line */
            $registry = \PHPUnit\Util\Annotation\Registry::getInstance();
        }

        /** @var array<string, mixed> $annotations */
        $annotations = rescue(
            fn () => $registry->forMethod(static::class, $this->getName(false))->symbolAnnotations(), [], false
        );

        Collection::make($annotations)
            ->filter(fn ($actions, $key) => $key === $name)
            ->each(function ($actions) use ($app) {
                (new Collection($actions ?? []))
                    ->filter(fn ($method) => \is_string($method) && method_exists($this, $method))
                    ->each(function ($method) use ($app) {
                        $this->{$method}($app);
                    });
            });
    }
}
