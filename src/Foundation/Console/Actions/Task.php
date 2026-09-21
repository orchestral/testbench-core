<?php

namespace Orchestra\Testbench\Foundation\Console\Actions;

use Closure;

class Task
{
    /**
     * Construct a new pending task.
     *
     * @template TActionResponse of bool
     *
     * @param  \Closure():(bool)  $requirement
     * @param  \Closure():(TActionResponse)  $action
     * @param  \Closure(TActionResponse):(void)  $response
     */
    public function __construct(
        protected Closure $requirement,
        protected Closure $action,
        protected Closure $response,
    ) {
        // ...
    }

    /**
     * Handle the task.
     */
    public function __invoke(bool $pretending = false): void
    {
        if (\call_user_func($this->requirement) === false) {
            return;
        }

        if ($pretending === true) {
            \call_user_func($this->response, true);

            return;
        }

        \call_user_func($this->response, \call_user_func($this->action));

    }
}
