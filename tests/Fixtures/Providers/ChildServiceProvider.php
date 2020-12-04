<?php

namespace Orchestra\Testbench\Tests\Fixtures\Providers;

class ChildServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app['child.loaded'] = true;
    }
}
