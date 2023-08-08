<?php

namespace Workbench\App\Providers;

use Illuminate\Support\ServiceProvider;

class TestbenchServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadMigrationsFrom(realpath(__DIR__.'/../../workbench/database/migrations'));
    }
}
