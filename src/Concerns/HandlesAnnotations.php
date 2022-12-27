<?php

namespace Orchestra\Testbench\Concerns;

use Illuminate\Support\Collection;
use function Orchestra\Testbench\phpunit_version_compare;
use PHPUnit\Framework\TestCase;
use PHPUnit\Metadata\Annotation\Parser\Registry as PHPUnit10Registry;
use PHPUnit\Util\Annotation\Registry as PHPUnit9Registry;
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

        [$registry, $methodName] = phpunit_version_compare('10', '>=')
            ? [PHPUnit10Registry::getInstance(), $this->name()] /** @phpstan-ignore-line */
            : [PHPUnit9Registry::getInstance(), $this->getName(false)]; /** @phpstan-ignore-line */

        /** @var array<string, mixed> $annotations */
        $annotations = rescue(
            fn () => $registry->forMethod(static::class, $methodName)->symbolAnnotations(),
            [],
            false
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

    /**
     * Clear parsed test method annotations.
     *
     * @phpunit-overrides
     *
     * @afterClass
     *
     * @return void
     */
    public static function clearParsedTestMethodAnnotations(): void
    {
        $registry = phpunit_version_compare('10', '>=')
            ? PHPUnit10Registry::getInstance() /** @phpstan-ignore-line */
            : PHPUnit9Registry::getInstance(); /** @phpstan-ignore-line */

        // Clear properties values from Registry class.
        (function () {
            $this->classDocBlocks = [];
            $this->methodDocBlocks = [];
        })->call($registry);
    }
}
