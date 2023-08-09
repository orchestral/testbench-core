<?php

namespace Orchestra\Testbench\Concerns;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use PHPUnit\Util\Annotation\Registry;

trait InteractsWithPHPUnit
{
    /**
     * The cached uses for test case.
     *
     * @var array<class-string, class-string>
     */
    protected static $cachedTestCaseUses = [];

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
     * @return bool
     */
    public static function usesTestingConcern(): bool
    {
        return isset(static::$cachedTestCaseUses[Testing::class]);
    }

    /**
     * Prepare the testing environment before the running the test case.
     *
     * @return void
     */
    public static function setupBeforeClassUsingPHPUnit(): void
    {
        /** @var array<class-string, class-string> $uses */
        $uses = array_flip(class_uses_recursive(static::class));

        static::$cachedTestCaseUses = $uses;
    }

    /**
     * Clean up the testing environment before the next test case.
     *
     * @return void
     */
    public static function teardownAfterClassUsingPHPUnit(): void
    {
        static::$cachedTestCaseUses = [];


        (function () {
            $this->classDocBlocks = [];
            $this->methodDocBlocks = [];
        })->call(Registry::getInstance());
    }
}
