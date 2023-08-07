<?php

namespace Orchestra\Testbench\Workbench\Console;

use Illuminate\Foundation\Console\ServeCommand as Command;
use function Orchestra\Testbench\workbench;

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

                    $this->components->info("Server running on [{$this->developmentUrl()}].");
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
        $workbench = workbench();

        $path = $workbench['start'];

        if (! \is_null($workbench['user'])) {
            $path = '/_workbench';
        }

        return "http://{$this->host()}:{$this->port()}{$path}";
    }
}
