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
     * @param  \Closure():(TActionResponse)  $action
     * @param  (\Closure(TActionResponse, bool):(void))|null  $response
     * @param  (\Closure():(bool))|bool  $requirement
     */
    public function __construct(
        protected Closure $action,
        protected ?Closure $response = null,
        protected Closure|bool $requirement = true,
    ) {
        // ...
    }

    /**
     * Handle the task.
     */
    public function __invoke(bool $pretending = false): void
    {
        if (value($this->requirement) === false) {
            return;
        }

        if ($pretending === true) {
            value($this->response, true, $pretending);

            return;
        }

        value($this->response, \call_user_func($this->action), $pretending);
    }
}
