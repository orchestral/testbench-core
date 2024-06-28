<?php

namespace Orchestra\Testbench\Bootstrap;

class RegisterProviders extends \Illuminate\Foundation\Bootstrap\RegisterProviders
{
    /**
     * Merge additional providers for Testbench.
     *
     * @internal
     *
     * @template TProviders of array<int, class-string>
     *
     * @param  TProviders  $providers
     * @return TProviders
     */
    public static function mergeAdditionalProvidersForTestbench(array $providers): array
    {
        if (
            static::$bootstrapProviderPath &&
            file_exists(static::$bootstrapProviderPath)
        ) {
            $packageProviders = require static::$bootstrapProviderPath;

            foreach ($packageProviders as $index => $provider) {
                if (! class_exists($provider)) {
                    unset($packageProviders[$index]);
                }
            }
        }

        return tap(
            array_merge($providers, static::$merge, array_values($packageProviders ?? [])),
            function ($p) {
                static::$merge = [];
                static::$bootstrapProviderPath = null;
            }
        );
    }
}
