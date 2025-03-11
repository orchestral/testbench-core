<?php

namespace Orchestra\Testbench\Foundation\Process;

use Illuminate\Process\ProcessResult;
use Illuminate\Support\Traits\ForwardsCalls;
use Symfony\Component\Process\Process;

/**
 * @mixin \Symfony\Component\Process\Process
 */
class ProcessDecorator extends ProcessResult
{
    use ForwardsCalls;

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
