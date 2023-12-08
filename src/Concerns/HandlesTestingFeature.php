<?php

namespace Orchestra\Testbench\Concerns;

use Closure;
use Illuminate\Support\Fluent;

trait HandlesTestingFeature
{
    /**
     * Resolve available testing features for Testbench.
     *
     * @param  (\Closure():(void))|null  $testCase
     * @param  (\Closure():(void))|null  $annotation
     * @param  (\Closure():(mixed))|null  $attribute
     * @param  (\Closure():(void))|null  $pest
     * @return \Illuminate\Support\Fluent<array-key, mixed>
     */
    protected function resolveTestbenchTestingFeature(
        ?Closure $testCase = null,
        ?Closure $annotation = null,
        ?Closure $attribute = null,
        ?Closure $pest = null
    ) {
        $result = new Fluent();

        value($annotation);
        $result->attribute = value($attribute);
        value($pest);
        value($testCase);

        return $result;
    }
}
