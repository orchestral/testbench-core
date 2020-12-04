<?php

namespace Orchestra\Testbench\Tests\Fixtures\Providers;

class CustomConfigServiceProvider extends \Illuminate\Support\ServiceProvider
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
