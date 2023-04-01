<?php

namespace Orchestra\Testbench\Foundation\Console;

use Illuminate\Support\Collection;
use Illuminate\Support\Env;
use Illuminate\Support\Str;
use NunoMaduro\Collision\Adapters\Laravel\Commands\TestCommand as Command;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'package:test
        {--without-tty : Disable output to TTY}
        {--parallel : Indicates if the tests should run in parallel}
        {--recreate-databases : Indicates if the test databases should be re-created}
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

        if (! \defined('TESTBENCH_WORKING_PATH')) {
            $this->setHidden(true);
        }
    }

    /**
     * Get the array of arguments for running PHPUnit.
     *
     * @param  array  $options
     * @return array
     */
    protected function phpunitArguments($options)
    {
        $options = Collection::make($options)
            ->merge(['--printer=NunoMaduro\\Collision\\Adapters\\Phpunit\\Printer'])
            ->reject(static function ($option) {
                return Str::startsWith($option, '--env=');
            })->values()->all();

        return array_merge(['--configuration=./'], $options);
    }

    /**
     * Get the array of arguments for running Paratest.
     *
     * @param  array  $options
     * @return array
     */
    protected function paratestArguments($options)
    {
        $options = Collection::make($options)
            ->reject(static function ($option) {
                return Str::startsWith($option, '--env=')
                    || Str::startsWith($option, '--parallel')
                    || Str::startsWith($option, '--recreate-databases');
            })->values()->all();

        return array_merge([
<<<<<<< HEAD
            '--configuration=./',
            "--runner=\Orchestra\Testbench\Foundation\ParallelRunner",
        ], $options);
=======
            'APP_KEY' => Env::get('APP_KEY'),
            'APP_DEBUG' => Env::get('APP_DEBUG'),
            'TESTBENCH_PACKAGE_TESTER' => '(true)',
        ], parent::phpunitEnvironmentVariables());
    }

    /**
     * Get the array of environment variables for running Paratest.
     *
     * @return array
     */
    protected function paratestEnvironmentVariables()
    {
        return array_merge([
            'APP_KEY' => Env::get('APP_KEY'),
            'APP_DEBUG' => Env::get('APP_DEBUG'),
            'TESTBENCH_PACKAGE_TESTER' => '(true)',
            'TESTBENCH_WORKING_PATH' => TESTBENCH_WORKING_PATH,
            'APP_BASE_PATH' => $this->laravel->basePath(),
        ], parent::paratestEnvironmentVariables());
>>>>>>> 597276d (Fixes configuration leaks via environment variables. (#107))
    }
}
