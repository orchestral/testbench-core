<?php

namespace Orchestra\Testbench\Features;

use Closure;
use Illuminate\Support\Fluent;
use Orchestra\Testbench\Concerns\HandlesAnnotations;
use Orchestra\Testbench\Concerns\HandlesAttributes;
use Orchestra\Testbench\Pest\WithPest;
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
     * @param  (\Closure(\Closure|null):(mixed))|null  $pest
     * @return \Illuminate\Support\Fluent<array-key, mixed>
     */
    public static function run(
        object $testCase,
        ?Closure $default = null,
        ?Closure $annotation = null,
        ?Closure $attribute = null,
        ?Closure $pest = null
    ): Fluent {
        /** @var \Illuminate\Support\Fluent{attribute: \Orchestra\Testbench\Features\FeaturesCollection} $result */
        $result = new Fluent(['attribute' => new FeaturesCollection()]);

        $calledDefault = false;
        $defaultCaller = function () use ($default, &$calledDefault) {
            if ($calledDefault === false) {
                value($default);
                $calledDefault = true;
            }
        };

        if ($testCase instanceof PHPUnitTestCase) {
            /** @phpstan-ignore-next-line */
            if ($testCase::usesTestingConcern(HandlesAnnotations::class)) {
                value($annotation, $defaultCaller);
            }

            /** @phpstan-ignore-next-line */
            if ($testCase::usesTestingConcern(HandlesAttributes::class)) {
                $result['attribute'] = value($attribute, $defaultCaller);
            }
        }

        /** @phpstan-ignore-next-line */
        if (
            $testCase instanceof PHPUnitTestCase
            && $pest instanceof Closure
            && $testCase::usesTestingConcern(WithPest::class)
        ) {
            value($pest, $defaultCaller);
        }

        value($defaultCaller);

        return $result;
    }
}
