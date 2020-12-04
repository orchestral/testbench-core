<?php

namespace Orchestra\Testbench\Tests\Fixtures\Providers;

use Illuminate\Support\AggregateServiceProvider;

class ParentServiceProvider extends AggregateServiceProvider
{
    protected $providers = [
        ChildServiceProvider::class,
        DeferredChildServiceProvider::class,
    ];

    public function register()
    {
        parent::register();

        $this->app['parent.loaded'] = true;
    }
}
