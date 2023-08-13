<?php

namespace Orchestra\Testbench\Workbench;

use Illuminate\Support\ServiceProvider;

class CommandServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Console\CreateSqliteDbCommand::class,
                Console\DropSqliteDbCommand::class,
            ]);
        }
    }
}
