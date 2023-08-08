<?php

namespace Orchestra\Testbench\Tests\Fixtures\Providers;

use Illuminate\Support\ServiceProvider;

class CustomConfigServiceProvider extends ServiceProvider
{
    public function register()
    {
        $config = [
            'foo' => 'bar',
        ];

        foreach ($config as $name => $params) {
            config(['database.redis.'.$name => $params]);
        }
    }
}
