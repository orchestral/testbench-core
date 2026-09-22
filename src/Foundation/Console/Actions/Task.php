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
     * Make a new pending task instance.
     *
     * @param  \Closure():(bool)  $action
     * @return static
     */
    public static function action(Closure $action)
    {
        return new static($action);
    }

    /**
     * Set the response callback for the task.
     *
     * @param  \Closure(bool, bool):(void)  $response
     * @return $this
     */
    public function response(Closure $response)
    {
        $this->response = $response;

        return $this;
    }

    /**
     * Set the requirement for the task.
     *
     * @param  (\Closure():(bool))|bool  $requirement
     * @return $this
     */
    public function requirements(Closure|bool $requirement)
    {
        $this->requirement = $requirement;

        return $this;
    }

    /**
     * Handle the task.
     *
     * @param  bool  $pretending
     * @return void
     */
    public function dispatch(bool $pretending = false): void
    {
        if (value($this->requirement) === false) {
            return;
        }

        if ($pretending === true) {
            /** @phpstan-ignore argument.type */
            value($this->response, true, $pretending);

            return;
        }

        /** @phpstan-ignore argument.type */
        value($this->response, \call_user_func($this->action), $pretending);
    }

    /**
     * Handle the task when invoked.
     *
     * @param  bool  $pretending
     * @return void
     */
    public function __invoke(bool $pretending = false): void
    {
        $this->dispatch($pretending);
    }
}
