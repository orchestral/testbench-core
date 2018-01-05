<?php

namespace Orchestra\Testbench\Tests\Stubs\Providers;

class ChildServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app['child.loaded'] = true;
    }
}
