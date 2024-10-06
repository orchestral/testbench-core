<?php

namespace Orchestra\Testbench\Foundation\Console;

use Composer\Config as ComposerConfig;
use Illuminate\Foundation\Console\ServeCommand as Command;
use Orchestra\Testbench\Foundation\Events\ServeCommandEnded;
use Orchestra\Testbench\Foundation\Events\ServeCommandStarted;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

use function Orchestra\Testbench\package_path;

class ServeCommand extends Command
{
    /**
     * Execute the console command.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     * @return int
     */
    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (
            class_exists(ComposerConfig::class, false)
            && method_exists(ComposerConfig::class, 'disableProcessTimeout') // @phpstan-ignore function.impossibleType
        ) {
            ComposerConfig::disableProcessTimeout();
        }

        $_ENV['TESTBENCH_WORKING_PATH'] = package_path();

        static::$passthroughVariables[] = 'TESTBENCH_WORKING_PATH';

        event(new ServeCommandStarted($input, $output, $this->components));

        return tap(parent::execute($input, $output), function ($exitCode) use ($input, $output) {
            event(new ServeCommandEnded($input, $output, $this->components, $exitCode));
        });
    }

    /**
     * Start a new server process.
     *
     * @param  bool  $hasEnvironment
     * @return \Symfony\Component\Process\Process
     */
    #[\Override]
    protected function startProcess($hasEnvironment)
    {
        return tap(parent::startProcess($hasEnvironment), function (Process $process) {
            $this->untrap();

            $this->trap(fn () => [SIGTERM, SIGINT, SIGHUP, SIGUSR1, SIGUSR2, SIGQUIT], function ($signal) use ($process) {
                if ($process->isRunning()) {
                    $process->stop(10, $signal);
                }
            });
        });
    }

    /**
     * Get the value of a command option.
     *
     * @param  string|null  $key
     * @return array|bool|string|null
     */
    #[\Override]
    public function option($key = null)
    {
        $value = parent::option($key);

        if ($key === 'no-reload' && $value !== true) {
            return true;
        }

        return $value;
    }
}
