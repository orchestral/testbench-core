<?php

namespace Orchestra\Testbench\Console;

use Illuminate\Contracts\Console\Kernel as ConsoleKernel;
use Orchestra\Testbench\Concerns\CreatesApplication;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;

class Commander
{
    use CreatesApplication;

    /**
     * Application instance.
     *
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * List of command providers.
     *
     * @var array
     */
    protected $providers = [];

    /**
     * Construct a new Commander.
     *
     * @param array $providers
     */
    public function __construct(array $providers = [])
    {
        $this->providers = $providers;
    }

    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return $this->providers;
    }

    /**
     * Handle the command.
     *
     * @return void
     */
    public function handle()
    {
        $laravel = $this->createApplication();
        $kernel = $laravel->make(ConsoleKernel::class);

        $status = $kernel->handle(
            $input = new ArgvInput, new ConsoleOutput
        );

        $kernel->terminate($input, $status);

        exit($status);
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application   $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        //
    }
}
