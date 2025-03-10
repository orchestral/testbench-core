<?php

namespace Orchestra\Testbench\Foundation\Actions;

use Illuminate\Support\Arr;
use Illuminate\Support\ProcessUtils;
use Symfony\Component\Process\Process;

use function Orchestra\Testbench\defined_environment_variables;
use function Orchestra\Testbench\package_path;
use function Orchestra\Testbench\php_binary;

class RemoteCommand
{
    /**
     * Construct a new action.
     *
     * @param  array<int, string>|string  $command
     * @param  array<string, mixed>|string  $env
     * @param  bool|null  $tty
     */
    public function __construct(
        public array|string $command,
        public array|string $env = [],
        public ?bool $tty = null
    ) {}

    /**
     * Execute the command.
     *
     * @return \Symfony\Component\Process\Process
     */
    public function __invoke(): Process
    {
        $binary = \defined('TESTBENCH_DUSK') ? 'testbench-dusk' : 'testbench';

        $commander = is_file($vendorBin = package_path('vendor', 'bin', $binary))
            ? ProcessUtils::escapeArgument((string) $vendorBin)
            : $binary;

        $env = \is_string($this->env) ? ['APP_ENV' => $this->env] : $this->env;

        Arr::add($env, 'TESTBENCH_PACKAGE_REMOTE', '(true)');

        $process = Process::fromShellCommandline(
            command: Arr::join([php_binary(true), $commander, ...Arr::wrap($this->command)], ' '),
            cwd: package_path(),
            env: array_merge(defined_environment_variables(), $env)
        );

        if (\is_bool($this->tty)) {
            $process->setTty($this->tty);
        }

        return $process;
    }
}
