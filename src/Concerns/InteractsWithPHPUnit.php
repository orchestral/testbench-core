<?php

namespace Orchestra\Testbench\Concerns;

use Illuminate\Support\Collection;
use Orchestra\Testbench\PHPUnit\AttributeParser;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use PHPUnit\Util\Annotation\Registry as PHPUnit9Registry;
use ReflectionClass;

/**
 * @internal
 *
 * @phpstan-import-type TTestingFeature from \Orchestra\Testbench\PHPUnit\AttributeParser
 */
trait InteractsWithPHPUnit
{
    use InteractsWithTestCase;

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
     * Determine if the trait is used within testing.
     *
     * @return bool
     */
    public function isRunningTestCase(): bool
    {
        return $this instanceof PHPUnitTestCase || static::usesTestingConcern();
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
            return new Collection;
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
     * @return \Illuminate\Support\Collection<class-string, array<int, object>>
     *
     * @phpstan-return \Illuminate\Support\Collection<class-string<TTestingFeature>, array<int, TTestingFeature>>
     */
    protected function resolvePhpUnitAttributes(): Collection
    {
        $instance = new ReflectionClass($this);

        if (! $this instanceof PHPUnitTestCase || $instance->isAnonymous()) {
            return new Collection; /** @phpstan-ignore return.type */
        }

        $className = $instance->getName();
        $methodName = $this->getName(false);

        return static::resolvePhpUnitAttributesForMethod($className, $methodName);
    }

    /**
     * Resolve PHPUnit method attributes for specific method.
     *
     * @phpunit-overrides
     *
     * @param  class-string  $className
     * @param  string|null  $methodName
     * @return \Illuminate\Support\Collection<class-string, array<int, object>>
     *
     * @phpstan-return \Illuminate\Support\Collection<class-string<TTestingFeature>, array<int, TTestingFeature>>
     */
    protected static function resolvePhpUnitAttributesForMethod(string $className, ?string $methodName = null): Collection
    {
        if (! isset(static::$cachedTestCaseClassAttributes[$className])) {
            static::$cachedTestCaseClassAttributes[$className] = rescue(static function () use ($className) {
                return AttributeParser::forClass($className);
            }, [], false);
        }

        if (! \is_null($methodName) && ! isset(static::$cachedTestCaseMethodAttributes["{$className}:{$methodName}"])) {
            static::$cachedTestCaseMethodAttributes["{$className}:{$methodName}"] = rescue(static function () use ($className, $methodName) {
                return AttributeParser::forMethod($className, $methodName);
            }, [], false);
        }

        /** @var \Illuminate\Support\Collection<class-string<TTestingFeature>, array<int, TTestingFeature>> $attributes */
        $attributes = Collection::make(array_merge(
            static::$testCaseTestingFeatures,
            static::$cachedTestCaseClassAttributes[$className],
            ! \is_null($methodName) ? static::$cachedTestCaseMethodAttributes["{$className}:{$methodName}"] : [],
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
