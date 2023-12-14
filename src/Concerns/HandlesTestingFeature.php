<?php

namespace Orchestra\Testbench\Concerns;

use Closure;
use Illuminate\Support\Fluent;
use Orchestra\Testbench\Pest\WithPest;
use Orchestra\Testbench\Attributes\CallbackCollection;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;

trait HandlesTestingFeature
{
    /**
     * Resolve available testing features for Testbench.
     *
     * @param  (\Closure():(void))|null  $default
     * @param  (\Closure():(void))|null  $annotation
     * @param  (\Closure():(mixed))|null  $attribute
     * @param  (\Closure(\Closure|null):(mixed))|null  $pest
     * @return \Illuminate\Support\Fluent<array-key, mixed>
     */
    protected function resolveTestbenchTestingFeature(
        ?Closure $default = null,
        ?Closure $annotation = null,
        ?Closure $attribute = null,
        ?Closure $pest = null
    ) {
        /** @var \Illuminate\Support\Fluent{attribute: \Orchestra\Testbench\Attributes\CallbackCollection} $result */
        $result = new Fluent(['attribute' => new CallbackCollection()]);

        if ($this instanceof PHPUnitTestCase && static::usesTestingConcern(HandlesAnnotations::class)) {
            value($annotation);
        }

        if ($this instanceof PHPUnitTestCase && static::usesTestingConcern(HandlesAttributes::class)) {
            $result['attribute'] = value($attribute);
        }

        /** @phpstan-ignore-next-line */
        if ($this instanceof PHPUnitTestCase && static::usesTestingConcern(WithPest::class)) {
            $pest instanceof Closure ? value($pest, $default) : value($default);
        } else {
            value($default);
        }

        return $result;
    }
}
