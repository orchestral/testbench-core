<?php

namespace Orchestra\Testbench\Tests\Fixtures\Providers;

use Illuminate\Support\ServiceProvider;

class ChildServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app['child.loaded'] = true;
    }
}
