<?php

namespace Orchestra\Testbench\Foundation\Console;

use Illuminate\Console\Command;
use RuntimeException;
use Symfony\Component\Process\Exception\ProcessSignaledException;
use Symfony\Component\Process\Process;

use function Orchestra\Testbench\package_path;

class TestFallbackCommand extends Command
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
     * @return int
     */
    public function handle()
    {
        if (! $this->confirm('Running tests requires "nunomaduro/collision". Do you wish to install it as a dev dependency?')) {
            return 1;
        }

        $this->installCollisionDependencies();

        return 0;
    }

    /**
     * Install testing needed dependencies.
     *
     * @return void
     */
    protected function installCollisionDependencies()
    {
        $command = \sprintf('%s require "nunomaduro/collision:^%s" --dev', $this->findComposer(), '6.2');

        $process = Process::fromShellCommandline($command, null, null, null, null);

        if ('\\' !== DIRECTORY_SEPARATOR && file_exists('/dev/tty') && is_readable('/dev/tty')) {
            try {
                $process->setTty(true);
            } catch (RuntimeException $e) {
                $this->output->writeln('Warning: '.$e->getMessage());
            }
        }

        try {
            $process->run(function ($type, $line) {
                $this->output->write($line);
            });
        } catch (ProcessSignaledException $e) {
            if (\extension_loaded('pcntl') && $e->getSignal() !== SIGINT) {
                throw $e;
            }
        }
    }

    /**
     * Get the composer command for the environment.
     *
     * @return string
     */
    protected function findComposer()
    {
        $composerPath = package_path('composer.phar');

        if (file_exists($composerPath)) {
            return '"'.PHP_BINARY.'" '.$composerPath;
        }

        return 'composer';
    }
}
