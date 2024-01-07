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

        /** @phpstan-ignore-next-line */
        if ($testCase instanceof PHPUnitTestCase && $testCase::usesTestingConcern(WithPest::class)) {
            $pest instanceof Closure ? value($pest, $default) : value($default);
        } else {
            value($default);
        }

        return $result;
    }
}
