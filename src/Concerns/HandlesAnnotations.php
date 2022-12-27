<?php

namespace Orchestra\Testbench\Concerns;

use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;
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

        /** @var array<string, mixed> $annotations */
        $annotations = rescue(
            fn () => PHPUnit9Registry::getInstance()->forMethod(static::class, $this->getName(false))->symbolAnnotations(),
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
        // Clear properties values from Registry class.
        (function () {
            $this->classDocBlocks = [];
            $this->methodDocBlocks = [];
        })->call(PHPUnit9Registry::getInstance());
    }
}
