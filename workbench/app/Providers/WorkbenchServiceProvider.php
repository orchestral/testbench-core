<?php

namespace Workbench\App\Providers;

use Illuminate\Support\ServiceProvider;

class WorkbenchServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->loadMigrationsFrom(realpath(__DIR__.'/../../database/migrations'));
    }
}
