<?php

namespace Orchestra\Testbench\Foundation\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\ProcessUtils;
use RuntimeException;
use Symfony\Component\Process\Exception\ProcessSignaledException;
use Symfony\Component\Process\Process;

use function Illuminate\Support\php_binary;
use function Laravel\Prompts\confirm;
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
        {--compact : Indicates whether the compact printer should be used}
        {--configuration= : Read configuration from XML file}
        {--coverage : Indicates whether the coverage information should be collected}
        {--min= : Indicates the minimum threshold enforcement for coverage}
        {--p|parallel : Indicates if the tests should run in parallel}
        {--profile : Lists top 10 slowest tests}
        {--recreate-databases : Indicates if the test databases should be re-created}
        {--drop-databases : Indicates if the test databases should be dropped}
        {--without-databases : Indicates if database configuration should be performed}
        {--c|--custom-argument : Add custom env variables}
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
        if (! confirm('Running tests requires "nunomaduro/collision". Do you wish to install it as a dev dependency?')) {
            return Command::FAILURE;
        }

        $this->installCollisionDependencies();

        return Command::SUCCESS;
    }

    /**
     * Install testing needed dependencies.
     *
     * @return void
     */
    protected function installCollisionDependencies(): void
    {
        $version = '8.0';

        $command = \sprintf('%s require "nunomaduro/collision:^%s" --dev', $this->findComposer(), $version);

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
    protected function findComposer(): string
    {
        $composerPath = package_path('composer.phar');

        if (file_exists($composerPath)) {
            return Collection::make([php_binary(), $composerPath])
                ->transform(fn ($path) => ProcessUtils::escapeArgument($path))
                ->join(' ');
        }

        return 'composer';
    }
}
