<?php

namespace Orchestra\Testbench\Database;

use Illuminate\Support\Arr;
use Illuminate\Database\Migrations\Migrator;

class MigrateProcessor
{
    /**
     * The migrator instance.
     *
     * @var \Illuminate\Database\Migrations\Migrator
     */
    protected $migrator;

    /**
     * The migrator options.
     *
     * @var array
     */
    protected $options = [];

    /**
     * Construct a new schema migrator.
     *
     * @param \Illuminate\Database\Migrations\Migrator  $migrator
     * @param array  $options
     */
    public function __construct(Migrator $migrator, array $options = [])
    {
        $this->migrator = $migrator;
        $this->options = $options;

        $this->setDatabaseConnection();
    }

    /**
     * Run migration.
     *
     * @return $this
     */
    public function up(): self
    {
        $this->install();

        $this->migrator->run($this->getMigrationPaths(), Arr::only($this->options, ['pretend', 'step']));

        return $this;
    }

    /**
     * Rollback migration.
     *
     * @return $this
     */
    public function rollback(): self
    {
        $this->migrator->rollback($this->getMigrationPaths(), Arr::only($this->options, ['pretend', 'step']));

        return $this;
    }

    /**
     * Install migration dependency.
     *
     * @return void
     */
    protected function install(): void
    {
        if (! $this->migrator->repositoryExists()) {
            $this->migrator->getRepository()->createRepository();
        }
    }

    /**
     * Get the migration paths.
     *
     * @return array|null
     */
    protected function getMigrationPaths(): ?array
    {
        $paths = $this->options['--path'] ?? $this->options['--realpath'] ?? null;

        if (is_string($paths)) {
            return [$paths];
        }

        return $paths;
    }

    /**
     * Set database migration.
     *
     * @return void
     */
    protected function setDatabaseConnection(): void
    {
        $database = $this->options['--database'] ?? null;

        if (! is_null($database)) {
            $this->migrator->setConnection($database);
        }
    }
}
