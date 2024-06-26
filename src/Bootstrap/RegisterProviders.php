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
        if (static::$bootstrapProviderPath &&
            file_exists(static::$bootstrapProviderPath)) {
            $packageProviders = require static::$bootstrapProviderPath;

            foreach ($packageProviders as $index => $provider) {
                if (! class_exists($provider)) {
                    unset($packageProviders[$index]);
                }
            }
        }

        $providers
            ->merge(static::$merge)
            ->merge(array_values($packageProviders ?? []));

        static::$merge = [];
        static::$bootstrapProviderPath = null;

        return $providers;
    }
}
