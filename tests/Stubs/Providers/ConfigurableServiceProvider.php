<?php

namespace Orchestra\Testbench\Tests\Stubs\Providers;

class ConfigurableServiceProvider extends ServiceProvider
{
    public static $configValue;

    public function register()
    {
        self::$configValue = config('test');
    }
}
