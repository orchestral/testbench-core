<?php

namespace Orchestra\Testbench\Concerns;

use Closure;
use Illuminate\Support\Fluent;
use Orchestra\Testbench\Pest\Hook;
use Orchestra\Testbench\Pest\WithPest;
use Pest\TestSuite;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;

trait HandlesTestingFeature
{
    /**
     * Resolve available testing features for Testbench.
     *
     * @param  (\Closure():(void))|null  $testCase
     * @param  (\Closure():(void))|null  $annotation
     * @param  (\Closure():(mixed))|null  $attribute
     * @param  string|null  $pest
     * @return \Illuminate\Support\Fluent<array-key, mixed>
     */
    protected function resolveTestbenchTestingFeature(
        ?Closure $testCase = null,
        ?Closure $annotation = null,
        ?Closure $attribute = null,
        ?string $pest = null
    ) {
        $result = new Fluent(['attribute' => null]);

        if ($this instanceof PHPUnitTestCase && static::usesTestingConcern(HandlesAnnotations::class)) {
            value($annotation);
        }

        if ($this instanceof PHPUnitTestCase && static::usesTestingConcern(HandlesAttributes::class)) {
            $result->attribute = value($attribute);
        }

        if ($this instanceof PHPUnitTestCase && static::usesTestingConcern(WithPest::class) && is_string($pest)) {
            value(Hook::unpack($pest, TestSuite::getInstance()->getFilename()));
        }

        value($testCase);

        return $result;
    }
}
