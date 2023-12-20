<?php

namespace Orchestra\Testbench\Concerns;

use Orchestra\Testbench\Contracts\Attributes\AfterAll as AfterAllContract;
use Orchestra\Testbench\Contracts\Attributes\BeforeAll as BeforeAllContract;
use Orchestra\Testbench\Contracts\Attributes\Resolvable as ResolvableContract;
use Orchestra\Testbench\PHPUnit\AttributeParser;

trait InteractsWithTestCase
{
    /**
     * The cached uses for test case.
     *
     * @var array<class-string, class-string>|null
     */
    protected static $cachedTestCaseUses;

    /**
     * The method attributes for test case.
     *
     * @var array<string, array<int, array{key: class-string, instance: object}>>
     *
     * @phpstan-var array<string, array<int, array{key: class-string<TTestingFeature>, instance: TTestingFeature}>>
     */
    protected static $testCaseTestingFeatures = [];

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
     * Uses testing feature (attribute) on the current test.
     *
     * @param  object  $attribute
     * @return void
     *
     * @phpstan-param TAttributes $attribute
     */
    public static function usesTestingFeature($attribute): void
    {
        if (! AttributeParser::validAttribute($attribute)) {
            return;
        }

        $attribute = $attribute instanceof ResolvableContract ? $attribute->resolve() : $attribute;

        if (\is_null($attribute)) {
            return;
        }

        /** @var class-string<TTestingFeature> $name */
        $name = \get_class($attribute);

        array_push(static::$testCaseTestingFeatures, [
            'key' => $name,
            'instance' => $attribute,
        ]);
    }

    /**
     * Prepare the testing environment before the running the test case.
     *
     * @return void
     *
     * @codeCoverageIgnore
     */
    public static function setUpBeforeClassUsingTestCase(): void
    {
        static::cachedUsesForTestCase();

        static::resolvePhpUnitAttributesForMethod(static::class)
            ->flatten()
            ->filter(fn ($instance) => $instance instanceof BeforeAllContract)
            ->map(function ($instance) {
                $instance->beforeAll();
            });
    }

    /**
     * Clean up the testing environment before the next test case.
     *
     * @return void
     *
     * @codeCoverageIgnore
     */
    public static function tearDownAfterClassUsingTestCase(): void
    {
        static::resolvePhpUnitAttributesForMethod(static::class)
            ->flatten()
            ->filter(fn ($instance) => $instance instanceof AfterAllContract)
            ->map(function ($instance) {
                $instance->afterAll();
            });

        static::$latestResponse = null;
        static::$cachedTestCaseUses = null;
        static::$testCaseTestingFeatures = [];
    }
}
