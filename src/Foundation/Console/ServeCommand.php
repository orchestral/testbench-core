<?php

namespace Orchestra\Testbench\Foundation\Console;

use Illuminate\Foundation\Console\ServeCommand as Command;

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
            return true;
        }

        return parent::option($key);
    }
}
