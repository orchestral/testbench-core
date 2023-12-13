<?php

namespace Orchestra\Testbench\Concerns;

use Closure;
use Illuminate\Support\Fluent;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;

trait HandlesTestingFeature
{
    /**
     * Resolve available testing features for Testbench.
     *
     * @param  (\Closure():(void))|null  $default
     * @param  (\Closure():(void))|null  $annotation
     * @param  (\Closure():(mixed))|null  $attribute
     * @return \Illuminate\Support\Fluent<array-key, mixed>
     */
    protected function resolveTestbenchTestingFeature(
        ?Closure $default = null,
        ?Closure $annotation = null,
        ?Closure $attribute = null
    ) {
        /** @var \Illuminate\Support\Fluent{?attribute: null} $result */
        $result = new Fluent();

        if ($this instanceof PHPUnitTestCase && static::usesTestingConcern(HandlesAnnotations::class)) {
            value($annotation);
        }

        if ($this instanceof PHPUnitTestCase && static::usesTestingConcern(HandlesAttributes::class)) {
            $result['attribute'] = value($attribute);
        }

        value($default);

        return $result;
    }
}
