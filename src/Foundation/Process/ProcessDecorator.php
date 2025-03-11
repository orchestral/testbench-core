<?php

namespace Orchestra\Testbench\Foundation\Process;

use Illuminate\Support\Traits\ForwardsCalls;
use Symfony\Component\Process\Process;

/**
 * @mixin \Symfony\Component\Process\Process
 */
class ProcessDecorator
{
    use ForwardsCalls;

    /**
     * Construct a new Process decorator.
     *
     * @param  \Symfony\Component\Process\Process  $process
     */
    public function __construct(
        protected Process $process
    ) {}

    /**
     * Handle dynamic calls to the process instance.
     *
     * @param  string  $method
     * @param  array<int, mixed>  $parameters
     * @return $this
     */
    public function __call($method, $parameters)
    {
        return $this->forwardDecoratedCallTo($this->process, $method, $parameters);
    }
}
