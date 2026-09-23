<?php

namespace Orchestra\Testbench\Foundation\Console\Actions;

use Illuminate\Console\Application as ConsoleApplication;
use Illuminate\Console\Command;
use Illuminate\Console\View\Components\Factory as ComponentsFactory;
use Orchestra\Sidekick\Console\Task;

class RunCommand extends Action
{
    /**
     * Construct a new action instance.
     *
     * @param  bool  $pretending
     */
    public function __construct(
        public Command|ConsoleApplication $console,
        public ?ComponentsFactory $components = null,
        bool $pretending = false,
    ) {
        $this->pretending = $pretending;
    }

    /**
     * Handle the action.
     *
     * @param  string  $name
     * @param  array<string, mixed>  $parameters
     * @return void
     */
    public function handle(string $name, array $parameters = []): void
    {
        Task::action(function () use ($name, $parameters) {
            $this->console->call($name, $parameters);

            return true;
        })->response(function ($action, $pretending) use ($name) {
            if ($pretending === true) {
                $this->components?->task(
                    \sprintf('Command [%s] executed', $name)
                );
            }
        })->dispatch($this->pretending);
    }
}
