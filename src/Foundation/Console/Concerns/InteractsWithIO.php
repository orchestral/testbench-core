<?php

namespace Orchestra\Testbench\Foundation\Console\Concerns;

trait InteractsWithIO
{
    /**
     * Write a status message to the console.
     *
     * @param  string  $from
     * @param  string  $to
     * @param  string  $type
     * @return void
     */
    protected function status(string $from, string $to, string $type): void
    {
        /** @phpstan-ignore-next-line */
        $workingPath = TESTBENCH_WORKING_PATH;

        $from = str_replace($workingPath.'/', '', realpath($from));

        $to = str_replace($workingPath.'/', '', realpath($to));

        $this->components->task(sprintf(
            'Copying %s [%s] to [%s]',
            $type,
            $from,
            $to,
        ));
    }
}
