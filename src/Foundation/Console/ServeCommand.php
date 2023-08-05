<?php

namespace Orchestra\Testbench\Foundation\Console;

use Illuminate\Foundation\Console\ServeCommand as Command;
use Orchestra\Testbench\Contracts\Config as ConfigContract;

class ServeCommand extends Command
{
    /**
     * Get the value of a command option.
     *
     * @param  string|null  $key
     * @return string|array|bool|null
     */
    public function option($key = null)
    {
        $value = parent::option($key);

        if ($key === 'no-reload' && $value !== true) {
            $value = true;
        }

        return $value;
    }

    /**
     * Returns a "callable" to handle the process output.
     *
     * @return callable(string, string): void
     */
    protected function handleProcessOutput()
    {
        $handler = parent::handleProcessOutput();

        return function ($type, $buffer) use ($handler) {
            str($buffer)->explode("\n")->each(function ($line) {
                if (str($line)->contains('Development Server (http')) {
                    if ($this->serverRunningHasBeenDisplayed) {
                        return;
                    }

                    $this->components->info("Server running on [http://{$this->host()}:{$this->port()}].");
                    $this->comment('  <fg=yellow;options=bold>Press Ctrl+C to stop the server</>');

                    $this->newLine();

                    $this->serverRunningHasBeenDisplayed = true;
                }
            });

            \call_user_func($handler, $type, $buffer);
        };
    }

    /**
     * The development url.
     *
     * @return string
     */
    protected function developmentUrl()
    {
        $config = $this->laravel->make(ConfigContract::class)->getWorkbenchAttributes();

        $path = $config['start'];

        if (! \is_null($config['user'])) {
            $path = '/_testbench';
        }

        return "http://{$this->host()}:{$this->port()}{$path}";
    }
}
