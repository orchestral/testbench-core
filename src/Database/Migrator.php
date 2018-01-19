<?php

namespace Orchestra\Testbench\Database;

use Illuminate\Support\Arr;
use Illuminate\Database\Migrations\Migrator as Illuminate;

class Migrator
{
    /**
     * The migrator instance.
     *
     * @var \Illuminate\Database\Migrations\Migrator
     */
    protected $migrator;

    /**
     * Construct a new schema migrator.
     *
     * @param \Illuminate\Database\Migrations\Migrator  $migrator
     */
    public function __construct(Illuminate $migrator)
    {
        $this->migrator = $migrator;
    }

    /**
     * Migrate.
     *
     * @param  array  $options
     *
     * @return $this
     */
    public function up(array $options): self
    {
        $this->setDatabaseConnection($options['--database'] ?? null);

        $this->install();

        $this->migrator->run($options['--path'], Arr::only($options, ['pretend', 'step']));

        return $this;
    }

    /**
     * Rollback migration.
     *
     * @param  array  $options
     *
     * @return $this
     */
    public function rollback(array $options): self
    {
        $this->setDatabaseConnection($options['--database'] ?? null);

        $this->migrator->rollback($options['--path'], Arr::only($options, ['pretend', 'step']));

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
     * Set database migration.
     *
     * @param  string  $database
     *
     * @return void
     */
    protected function setDatabaseConnection(?string $database): void
    {
        if (! is_null($database)) {
            $this->migrator->setConnection($database);
        }
    }
}
