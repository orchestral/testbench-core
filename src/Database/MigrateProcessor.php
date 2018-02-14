<?php

namespace Orchestra\Testbench\Database;

use Orchestra\Testbench\Contracts\TestCase;
use Illuminate\Database\Migrations\Migrator;

class MigrateProcessor
{
    /**
     * The testbench instance.
     *
     * @var \Orchestra\Testbench\Contracts\TestCase
     */
    protected $testbench;

    /**
     * The migrator options.
     *
     * @var array
     */
    protected $options = [];

    /**
     * Construct a new schema migrator.
     *
     * @param \Orchestra\Testbench\Contracts\TestCase  $testbench
     * @param array  $options
     */
    public function __construct(TestCase $testbench, array $options = [])
    {
        $this->testbench = $testbench;
        $this->options = $options;
    }

    /**
     * Run migration.
     *
     * @return $this
     */
    public function up(): self
    {
        $this->testbench->artisan('migrate', $this->options);

        return $this;
    }

    /**
     * Rollback migration.
     *
     * @return $this
     */
    public function rollback(): self
    {
        $this->testbench->artisan('migrate:rollback', $this->options);

        return $this;
    }
}
