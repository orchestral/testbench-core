<?php

namespace Orchestra\Testbench\Database;

use function Orchestra\Testbench\artisan;
use Orchestra\Testbench\Contracts\TestCase;

class MigrateProcessor
{
    /**
     * Construct a new schema migrator.
     *
     * @param  \Orchestra\Testbench\Contracts\TestCase  $testbench
     * @param  array<string, mixed>  $options
     */
    public function __construct(
        protected TestCase $testbench,
        protected array $options = []
    ) {
    }

    /**
     * Run migration.
     *
     * @return $this
     */
    public function up()
    {
        $this->dispatch('migrate');

        return $this;
    }

    /**
     * Rollback migration.
     *
     * @return $this
     */
    public function rollback()
    {
        $this->dispatch('migrate:rollback');

        return $this;
    }

    /**
     * Dispatch artisan command.
     *
     * @param  string  $command
     * @return void
     */
    protected function dispatch(string $command): void
    {
        artisan($this->testbench, $command, $this->options);
    }
}
