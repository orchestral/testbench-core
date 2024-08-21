<?php

namespace Orchestra\Testbench\Foundation\Console;

use Illuminate\Support\Collection;
use NunoMaduro\Collision\Adapters\Laravel\Commands\TestCommand as Command;
use Orchestra\Testbench\Foundation\Env;

use function Orchestra\Testbench\defined_environment_variables;
use function Orchestra\Testbench\package_path;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'package:test
        {--without-tty : Disable output to TTY}
        {--c|configuration= : Read configuration from XML file}
        {--compact : Indicates whether the compact printer should be used}
        {--coverage : Indicates whether code coverage information should be collected}
        {--min= : Indicates the minimum threshold enforcement for code coverage}
        {--p|parallel : Indicates if the tests should run in parallel}
        {--profile : Lists top 10 slowest tests}
        {--recreate-databases : Indicates if the test databases should be re-created}
        {--drop-databases : Indicates if the test databases should be dropped}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the package tests';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        if (! \defined('TESTBENCH_CORE')) {
            $this->setHidden(true);
        }
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Env::enablePutenv();

        return parent::handle();
    }

    /**
     * Get the PHPUnit configuration file path.
     *
     * @return string
     */
    public function phpUnitConfigurationFile()
    {
        $configurationFile = str_replace('./', '', $this->option('configuration') ?? 'phpunit.xml');

        return Collection::make([
            package_path($configurationFile),
            package_path("{$configurationFile}.dist"),
        ])->transform(static fn ($path) => DIRECTORY_SEPARATOR.$path)
            ->filter(static fn ($path) => file_exists($path))
            ->first() ?? './';
    }

    /**
     * Get the array of arguments for running PHPUnit.
     *
     * @param  array  $options
     * @return array
     */
    protected function phpunitArguments($options)
    {
        $file = $this->phpUnitConfigurationFile();

        return Collection::make(parent::phpunitArguments($options))
            ->reject(static function ($option) {
                return str_starts_with($option, '--configuration=');
            })->merge(["--configuration={$file}"])
            ->all();
    }

    /**
     * Get the array of arguments for running Paratest.
     *
     * @param  array  $options
     * @return array
     */
    protected function paratestArguments($options)
    {
        $file = $this->phpUnitConfigurationFile();

        return Collection::make(parent::paratestArguments($options))
            ->reject(static function (string $option) {
                return str_starts_with($option, '--configuration=')
                    || str_starts_with($option, '--runner=');
            })->merge([
                "--configuration={$file}",
                "--runner=\Orchestra\Testbench\Foundation\ParallelRunner",
            ])->all();
    }

    /**
     * Get the array of environment variables for running PHPUnit.
     *
     * @return array
     */
    protected function phpunitEnvironmentVariables()
    {
        return Collection::make(defined_environment_variables())
            ->merge([
                'APP_ENV' => 'testing',
                'TESTBENCH_PACKAGE_TESTER' => '(true)',
                'TESTBENCH_WORKING_PATH' => package_path(),
                'TESTBENCH_APP_BASE_PATH' => $this->laravel->basePath(),
            ])->merge(parent::phpunitEnvironmentVariables())
            ->all();
    }

    /**
     * Get the array of environment variables for running Paratest.
     *
     * @return array
     */
    protected function paratestEnvironmentVariables()
    {
        return Collection::make(defined_environment_variables())
            ->merge([
                'APP_ENV' => 'testing',
                'TESTBENCH_PACKAGE_TESTER' => '(true)',
                'TESTBENCH_WORKING_PATH' => package_path(),
                'TESTBENCH_APP_BASE_PATH' => $this->laravel->basePath(),
            ])->merge(parent::paratestEnvironmentVariables())
            ->all();
    }
}
