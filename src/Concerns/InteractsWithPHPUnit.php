<?php

namespace Orchestra\Testbench\Concerns;

use Closure;
use Illuminate\Support\Collection;
use Orchestra\Testbench\Contracts\Attributes\Resolvable as ResolvableContract;
use Orchestra\Testbench\PHPUnit\AttributeParser;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use PHPUnit\Metadata\Annotation\Parser\Registry as PHPUnit10Registry;
use PHPUnit\Util\Annotation\Registry as PHPUnit9Registry;
use ReflectionClass;

use function Orchestra\Testbench\phpunit_version_compare;

/**
 * @internal
 *
 * @phpstan-import-type TTestingFeature from \Orchestra\Testbench\PHPUnit\AttributeParser
 * @phpstan-import-type TAttributes from \Orchestra\Testbench\PHPUnit\AttributeParser
 */
trait InteractsWithPHPUnit
{
    /**
     * The cached test case setUp resolver.
     *
     * @var (\Closure(\Closure):(void))|null
     */
    protected $testCaseSetUpCallback;

    /**
     * The cached test case tearDown resolver.
     *
     * @var (\Closure(\Closure):(void))|null
     */
    protected $testCaseTearDownCallback;

    /**
     * The cached uses for test case.
     *
     * @var array<class-string, class-string>|null
     */
    protected static $cachedTestCaseUses;

    /**
     * The cached class attributes for test case.
     *
     * @var array<string, array<int, array{key: class-string, instance: object}>>
     *
     * @phpstan-var array<string, array<int, array{key: class-string<TTestingFeature>, instance: TTestingFeature}>>
     */
    protected static $cachedTestCaseClassAttributes = [];

    /**
     * The cached method attributes for test case.
     *
     * @var array<string, array<int, array{key: class-string, instance: object}>>
     *
     * @phpstan-var array<string, array<int, array{key: class-string<TTestingFeature>, instance: TTestingFeature}>>
     */
    protected static $cachedTestCaseMethodAttributes = [];

    /**
     * The method attributes for test case.
     *
     * @var array<string, array<int, array{key: class-string, instance: object}>>
     *
     * @phpstan-var array<string, array<int, array{key: class-string<TTestingFeature>, instance: TTestingFeature}>>
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
     * @param  object  $attribute
     * @return void
     *
     * @phpstan-param TAttributes $attribute
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
        $methodName = phpunit_version_compare('10', '>=')
            ? $this->name() /** @phpstan-ignore-line */
            : $this->getName(false); /** @phpstan-ignore-line */
        if (! isset(static::$testCaseMethodAttributes["{$className}:{$methodName}"])) {
            static::$testCaseMethodAttributes["{$className}:{$methodName}"] = [];
        }

        /** @var class-string<TTestingFeature> $name */
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

        [$registry, $methodName] = phpunit_version_compare('10', '>=')
            ? [PHPUnit10Registry::getInstance(), $this->name()] /** @phpstan-ignore-line */
            : [PHPUnit9Registry::getInstance(), $this->getName(false)]; /** @phpstan-ignore-line */

        /** @var array<string, mixed> $annotations */
        $annotations = rescue(
            fn () => $registry->forMethod($instance->getName(), $methodName)->symbolAnnotations(),
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
     * @return \Illuminate\Support\Collection<class-string, array<int, object>>
     *
     * @phpstan-return \Illuminate\Support\Collection<class-string<TTestingFeature>, array<int, TTestingFeature>>
     */
    protected function resolvePhpUnitAttributes(): Collection
    {
        $instance = new ReflectionClass($this);

        if (! $this instanceof PHPUnitTestCase || $instance->isAnonymous()) {
            return new Collection();
        }

        $className = $instance->getName();
        $methodName = phpunit_version_compare('10', '>=')
            ? $this->name() /** @phpstan-ignore-line */
            : $this->getName(false); /** @phpstan-ignore-line */
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

        /** @var \Illuminate\Support\Collection<class-string<TTestingFeature>, array<int, TTestingFeature>> $attributes */
        $attributes = Collection::make(array_merge(
            static::$cachedTestCaseClassAttributes[$className],
            static::$cachedTestCaseMethodAttributes["{$className}:{$methodName}"],
            static::$testCaseMethodAttributes["{$className}:{$methodName}"] ?? [],
        ))->groupBy('key')
            ->map(static function ($attributes) {
                /** @var \Illuminate\Support\Collection<int, array{key: class-string<TTestingFeature>, instance: TTestingFeature}> $attributes */
                return $attributes->map(static function ($attribute) {
                    /** @var array{key: class-string<TTestingFeature>, instance: TTestingFeature} $attribute */
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
     * Define the setUp environment using callback.
     *
     * @param  \Closure(\Closure):void  $setUp
     * @return void
     *
     * @codeCoverageIgnore
     */
    public function setUpTheEnvironmentUsing(Closure $setUp): void
    {
        $this->testCaseSetUpCallback = $setUp;
    }

    /**
     * Define the tearDown environment using callback.
     *
     * @param  \Closure(\Closure):void  $tearDown
     * @return void
     *
     * @codeCoverageIgnore
     */
    public function tearDownTheEnvironmentUsing(Closure $tearDown): void
    {
        $this->testCaseTearDownCallback = $tearDown;
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

        $registry = phpunit_version_compare('10', '>=')
            ? PHPUnit10Registry::getInstance() /** @phpstan-ignore-line */
            : PHPUnit9Registry::getInstance(); /** @phpstan-ignore-line */
        (function () {
            $this->classDocBlocks = [];
            $this->methodDocBlocks = [];
        })->call($registry);
    }
}
