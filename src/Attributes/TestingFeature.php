<?php

namespace Orchestra\Testbench\Attributes;

use Closure;
use Illuminate\Support\Fluent;
use Orchestra\Testbench\Concerns\HandlesAnnotations;
use Orchestra\Testbench\Concerns\HandlesAttributes;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;

/**
 * @internal
 */
final class TestingFeature
{
    /**
     * Resolve available testing features for Testbench.
     *
     * @param  object  $testCase
     * @param  (\Closure():(void))|null  $default
     * @param  (\Closure():(void))|null  $annotation
     * @param  (\Closure():(mixed))|null  $attribute
     * @return \Illuminate\Support\Fluent
     */
    public static function run(
        object $testCase,
        ?Closure $default = null,
        ?Closure $annotation = null,
        ?Closure $attribute = null
    ): Fluent {
        /** @var \Illuminate\Support\Fluent{attribute: \Orchestra\Testbench\Attributes\FeaturesCollection} $result */
        $result = new Fluent(['attribute' => new FeaturesCollection()]);

        if ($testCase instanceof PHPUnitTestCase) {
            /** @phpstan-ignore-next-line */
            if ($testCase::usesTestingConcern(HandlesAnnotations::class)) {
                value($annotation);
            }

            /** @phpstan-ignore-next-line */
            if ($testCase::usesTestingConcern(HandlesAttributes::class)) {
                $result['attribute'] = value($attribute);
            }
        }

        value($default);

        return $result;
    }
}
