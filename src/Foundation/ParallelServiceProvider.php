<?php

namespace Orchestra\Testbench\Foundation;

use Illuminate\Support\ServiceProvider;
use Illuminate\Testing\ParallelRunner;
use Orchestra\Testbench\Concerns\CreatesApplication;

class ParallelServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        ParallelRunner::resolveApplicationUsing(function () {
            $applicationCreator = new class {
                use CreatesApplication;
            };

            return $applicationCreator->createApplication();
        });
}
