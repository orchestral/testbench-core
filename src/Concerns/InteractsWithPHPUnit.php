<?php

namespace Orchestra\Testbench\Concerns;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use PHPUnit\Util\Annotation\Registry;

trait InteractsWithPHPUnit
{
    /**
     * The cached uses for test case.
     *
     * @var array<class-string, class-string>|null
     */
    protected static $cachedTestCaseUses;

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
     * Determine if the trait is used Orchestra\Testbench\Concerns\Testing trait.
     *
     * @param  class-string|null  $trait
     * @return bool
     */
    public static function usesTestingConcern(?string $trait = null): bool
    {
        static::defineCachedTestCaseUses();

        return isset(static::$cachedTestCaseUses[$trait ?? Testing::class]);
    }

    /**
     * Define cached test case uses.
     *
     * @return void
     */
    public static function defineCachedTestCaseUses(): void
    {
        if (\is_null(static::$cachedTestCaseUses)) {
            /** @var array<class-string, class-string> $uses */
            $uses = array_flip(class_uses_recursive(static::class));

            static::$cachedTestCaseUses = $uses;
        }
    }

    /**
     * Prepare the testing environment before the running the test case.
     *
     * @return void
     */
    public static function setupBeforeClassUsingPHPUnit(): void
    {
        static::defineCachedTestCaseUses();
    }

    /**
     * Clean up the testing environment before the next test case.
     *
     * @return void
     */
    public static function teardownAfterClassUsingPHPUnit(): void
    {
        static::$cachedTestCaseUses = null;

        (function () {
            $this->classDocBlocks = [];
            $this->methodDocBlocks = [];
        })->call(Registry::getInstance());
    }
}
