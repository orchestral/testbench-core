<?php

namespace Orchestra\Testbench\Tests\Fixtures\Providers;

use Illuminate\Support\ServiceProvider;

class DeferredChildServiceProvider extends ServiceProvider
{
    protected $defer = true;

    public function register()
    {
        $this->app['child.deferred.loaded'] = true;
    }
}
