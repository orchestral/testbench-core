<?php

namespace Orchestra\Testbench\Foundation\Console;

use Composer\Config as ComposerConfig;
use Illuminate\Foundation\Auth\User;
use Illuminate\Foundation\Console\ServeCommand as Command;
use Orchestra\Testbench\Foundation\Events\ServeCommandEnded;
use Orchestra\Testbench\Foundation\Events\ServeCommandStarted;
use Orchestra\Testbench\Workbench\Workbench;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

use function Orchestra\Testbench\package_path;

/**
 * @codeCoverageIgnore
 */
class ServeCommand extends Command
{
    /** {@inheritDoc} */
    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (
            class_exists(ComposerConfig::class, false)
            && method_exists(ComposerConfig::class, 'disableProcessTimeout') // @phpstan-ignore function.impossibleType
        ) {
            ComposerConfig::disableProcessTimeout();
        }

        $serverWorkers = getenv('PHP_CLI_SERVER_WORKERS');

        if (\is_string($serverWorkers) && filter_var($serverWorkers, FILTER_VALIDATE_INT) && ! isset($_ENV['PHP_CLI_SERVER_WORKERS'])) {
            /** @var int<2, max>|false $workers */
            $workers = transform(
                $serverWorkers,
                static fn (int $workers) => $workers > 1 ? $workers : false // @phpstan-ignore argument.type
            );

            $this->phpServerWorkers = $workers;
        }

        $this->addPassThroughEnvironmentVariable('TESTBENCH_WORKING_PATH', package_path());
        $this->addPassThroughEnvironmentVariable('TESTBENCH_USER_MODEL', Workbench::applicationUserModel() ?? User::class);

        event(new ServeCommandStarted($input, $output, $this->components));

        return tap(parent::execute($input, $output), function ($exitCode) use ($input, $output) {
            event(new ServeCommandEnded($input, $output, $this->components, $exitCode));
        });
    }

    /**
     * Add an environment variable that should be passed through to the server process.
     *
     * @param  string  $name
     * @param  mixed  $value
     * @return void
     */
    protected function addPassThroughEnvironmentVariable(string $name, mixed $value): void
    {
        $_ENV[$name] = $value;

        static::$passthroughVariables[] = $name;
    }

    /** {@inheritDoc} */
    #[\Override]
    protected function startProcess($hasEnvironment)
    {
        return tap(parent::startProcess($hasEnvironment), function (Process $process) {
            TerminatingConsole::before(static function ($signal) use ($process) {
                if ($process->isRunning()) {
                    $process->stop(10, $signal);
                }
            });
        });
    }

    /** {@inheritDoc} */
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
