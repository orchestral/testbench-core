<?php

namespace Orchestra\Testbench\Concerns;

use Illuminate\Support\Collection;
use Orchestra\Testbench\Contracts\Attributes\Resolvable as ResolvableContract;
use Orchestra\Testbench\PHPUnit\AttributeParser;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use PHPUnit\Util\Annotation\Registry as PHPUnit9Registry;
use ReflectionClass;

trait InteractsWithPHPUnit
{
    /**
     * The cached uses for test case.
     *
     * @var array<class-string, class-string>|null
     */
    protected static $cachedTestCaseUses;

    /**
     * The cached class attributes for test case.
     *
     * @var array<string, array<int, array{key: class-string<\Orchestra\Testbench\Contracts\Attributes\TestingFeature>, instance: \Orchestra\Testbench\Contracts\Attributes\TestingFeature}>>
     */
    protected static $cachedTestCaseClassAttributes = [];

    /**
     * The cached method attributes for test case.
     *
     * @var array<string, array<int, array{key: class-string<\Orchestra\Testbench\Contracts\Attributes\TestingFeature>, instance: \Orchestra\Testbench\Contracts\Attributes\TestingFeature}>>
     */
    protected static $cachedTestCaseMethodAttributes = [];

    /**
     * The method attributes for test case.
     *
     * @var array<string, array<int, array{key: class-string<\Orchestra\Testbench\Contracts\Attributes\TestingFeature>, instance: \Orchestra\Testbench\Contracts\Attributes\TestingFeature}>>
     */
    protected static $testCaseMethodAttributes = [];

    /**
     * Determine if the trait is used within testing.
     *
     * @return bool
     */
    public function isRunningTestCase(): bool
    {
        return $this instanceof PHPUnitTestCase || static::usesTestingConcern();
    }

    /**
     * Uses testing feature (attribute) on the current test.
     *
     * @param  \Orchestra\Testbench\Contracts\Attributes\TestingFeature|\Orchestra\Testbench\Contracts\Attributes\Resolvable  $attribute
     * @return void
     */
    public function usesTestingFeature($attribute): void
    {
        $instance = new ReflectionClass($this);

        if (
            ! $this instanceof PHPUnitTestCase
            || ! AttributeParser::validAttribute($attribute)
            || $instance->isAnonymous()
        ) {
            return;
        }

        $attribute = $attribute instanceof ResolvableContract ? $attribute->resolve() : $attribute;

        if (\is_null($attribute)) {
            return;
        }

        $className = $instance->getName();
        $methodName = $this->getName(false);

        if (! isset(static::$testCaseMethodAttributes["{$className}:{$methodName}"])) {
            static::$testCaseMethodAttributes["{$className}:{$methodName}"] = [];
        }

        /** @var class-string<\Orchestra\Testbench\Contracts\Attributes\TestingFeature> $name */
        $name = \get_class($attribute);

        array_push(static::$testCaseMethodAttributes["{$className}:{$methodName}"], [
            'key' => $name,
            'instance' => $attribute,
        ]);
    }

    /**
     * Resolve PHPUnit method annotations.
     *
     * @phpunit-overrides
     *
     * @return \Illuminate\Support\Collection<string, mixed>
     */
    protected function resolvePhpUnitAnnotations(): Collection
    {
        $instance = new ReflectionClass($this);

        if (! $this instanceof PHPUnitTestCase || $instance->isAnonymous()) {
            return new Collection();
        }

        /** @var array<string, mixed> $annotations */
        $annotations = rescue(
            fn () => PHPUnit9Registry::getInstance()->forMethod($instance->getName(), $this->getName(false))->symbolAnnotations(),
            [],
            false
        );

        return Collection::make($annotations);
    }

    /**
     * Resolve PHPUnit method attributes.
     *
     * @phpunit-overrides
     *
     * @return \Illuminate\Support\Collection<class-string<\Orchestra\Testbench\Contracts\Attributes\TestingFeature>, array<int, \Orchestra\Testbench\Contracts\Attributes\TestingFeature>>
     */
    protected function resolvePhpUnitAttributes(): Collection
    {
        $instance = new ReflectionClass($this);

        if (! $this instanceof PHPUnitTestCase || $instance->isAnonymous()) {
            return new Collection();
        }

        $className = $instance->getName();
        $methodName = $this->getName(false);

        if (! isset(static::$cachedTestCaseClassAttributes[$className])) {
            static::$cachedTestCaseClassAttributes[$className] = rescue(static function () use ($className) {
                return AttributeParser::forClass($className);
            }, [], false);
        }

        if (! isset(static::$cachedTestCaseMethodAttributes["{$className}:{$methodName}"])) {
            static::$cachedTestCaseMethodAttributes["{$className}:{$methodName}"] = rescue(static function () use ($className, $methodName) {
                return AttributeParser::forMethod($className, $methodName);
            }, [], false);
        }

        /** @var \Illuminate\Support\Collection<class-string<\Orchestra\Testbench\Contracts\Attributes\TestingFeature>, array<int, \Orchestra\Testbench\Contracts\Attributes\TestingFeature>> $attributes */
        $attributes = Collection::make(array_merge(
            static::$cachedTestCaseClassAttributes[$className],
            static::$cachedTestCaseMethodAttributes["{$className}:{$methodName}"],
            static::$testCaseMethodAttributes["{$className}:{$methodName}"] ?? [],
        ))->groupBy('key')
            ->transform(static function ($attributes) {
                /** @var \Illuminate\Support\Collection<int, array{key: class-string<\Orchestra\Testbench\Contracts\Attributes\TestingFeature>, instance: \Orchestra\Testbench\Contracts\Attributes\TestingFeature}> $attributes */
                return $attributes->transform(static function ($attribute) {
                    return $attribute['instance'];
                });
            });

        return $attributes;
    }

    /**
     * Determine if the trait is used Orchestra\Testbench\Concerns\Testing trait.
     *
     * @param  class-string|null  $trait
     * @return bool
     */
    public static function usesTestingConcern(?string $trait = null): bool
    {
        return isset(static::cachedUsesForTestCase()[$trait ?? Testing::class]);
    }

    /**
     * Define or get the cached uses for test case.
     *
     * @return array<class-string, class-string>
     */
    public static function cachedUsesForTestCase(): array
    {
        if (\is_null(static::$cachedTestCaseUses)) {
            /** @var array<class-string, class-string> $uses */
            $uses = array_flip(class_uses_recursive(static::class));

            static::$cachedTestCaseUses = $uses;
        }

        return static::$cachedTestCaseUses;
    }

    /**
     * Prepare the testing environment before the running the test case.
     *
     * @return void
     *
     * @codeCoverageIgnore
     */
    public static function setUpBeforeClassUsingPHPUnit(): void
    {
        static::cachedUsesForTestCase();
    }

    /**
     * Clean up the testing environment before the next test case.
     *
     * @return void
     *
     * @codeCoverageIgnore
     */
    public static function tearDownAfterClassUsingPHPUnit(): void
    {
        static::$cachedTestCaseUses = null;
        static::$cachedTestCaseClassAttributes = [];
        static::$cachedTestCaseMethodAttributes = [];

        (function () {
            $this->classDocBlocks = [];
            $this->methodDocBlocks = [];
        })->call(PHPUnit9Registry::getInstance());
    }
}
