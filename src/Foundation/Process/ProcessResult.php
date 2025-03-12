<?php

namespace Orchestra\Testbench\Foundation\Process;

use Closure;
use Symfony\Component\Process\Process;

class ProcessResult extends \Illuminate\Process\ProcessResult
{
    /**
     * Create a new process decorator instance.
     *
     * @param  \Symfony\Component\Process\Process  $process
     * @param  (\Closure():(mixed))|array<int, string>|string  $command
     */
    public function __construct(
        Process $process,
        protected Closure|array|string $command,
    ) {
        parent::__construct($process);
    }

    /** {@inheritDoc} */
    #[\Override]
    public function output()
    {
        $output = $this->process->getOutput();

        if (! $this->command instanceof Closure) {
            return $output;
        }

        $result = json_decode($output, true);

        if (! $result['successful']) {
            throw new $result['exception'](
                ...(! empty(array_filter($result['parameters']))
                    ? $result['parameters']
                    : [$result['message']])
            );
        }

        return unserialize($result['result']);
    }
}
