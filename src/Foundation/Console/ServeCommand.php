<?php

namespace Orchestra\Testbench\Foundation\Console;

use Illuminate\Foundation\Console\ServeCommand as Command;
use RuntimeException;

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
        if ($key === 'no-reload') {
            throw new RuntimeException('Unable to use "no-reload" option on Serve command running within Testbench');
        }

        return parent::option($key);
    }
}
