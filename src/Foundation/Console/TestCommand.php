<?php

namespace Orchestra\Testbench\Foundation\Console;

use Illuminate\Support\Collection;
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
        {--coverage : Indicates whether the coverage information should be collected}
        {--min= : Indicates the minimum threshold enforcement for coverage}
        {--parallel : Indicates if the tests should run in parallel}
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

        if (! \defined('TESTBENCH_WORKING_PATH')) {
            $this->setHidden(true);
        }
    }

    /**
     * @return string
     */
    public function getPhpUnitFile()
    {
        if (!file_exists($file = TESTBENCH_WORKING_PATH . '/phpunit.xml')) {
            $file = TESTBENCH_WORKING_PATH . '/phpunit.xml.dist';
        }
        return $file;
    }

    /**
     * Get the array of arguments for running PHPUnit.
     *
     * @param  array  $options
     * @return array
     */

    protected function phpunitArguments($options)
    {
        $parentOptions = parent::phpunitArguments($options);
        $filteredParentOptions = array_filter($parentOptions, function (string $option) {
            return ! Str::startsWith($option, ['--configuration=']);
        });

        $file = $this->getPhpUnitFile();

        return array_merge(["--configuration=$file",], $filteredParentOptions);

    }

    /**
     * Get the array of arguments for running Paratest.
     *
     * @param  array  $options
     * @return array
     */
    protected function paratestArguments($options)
    {
        $parentOptions = parent::paratestArguments($options);
        $filteredParentOptions = array_filter($parentOptions, function (string $option) {
            return ! Str::startsWith($option, ['--configuration=','--runner=']);
        });

        $file = $this->getPhpUnitFile();

        return array_merge([
            "--configuration=$file",
            "--runner=\Orchestra\Testbench\Foundation\ParallelRunner",
        ], $filteredParentOptions);
    }

    /**
     * Get the array of environment variables for running PHPUnit.
     *
     * @return array
     */
    protected function phpunitEnvironmentVariables()
    {
        return array_merge([
            'TESTBENCH_PACKAGE_TESTER' => 1,
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
            'TESTBENCH_PACKAGE_TESTER' => 1,
        ], parent::paratestEnvironmentVariables());
    }
}
