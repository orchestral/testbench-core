<?php

namespace Orchestra\Testbench\Bootstrap;

use Illuminate\Support\Collection;

class RegisterProviders extends \Illuminate\Foundation\Bootstrap\RegisterProviders
{
    /**
     * Merge additional providers for Testbench.
     *
     * @internal
     *
     * @template TProvidersCollection of \Illuminate\Support\Collection
     *
     * @param  TProvidersCollection  $providers
     * @return TProvidersCollection
     */
    public static function mergeAdditionalProvidersForTestbench(Collection $providers): Collection
    {
        $providers->merge(static::$merge);

        static::$merge = [];

        return $providers;
    }
}
