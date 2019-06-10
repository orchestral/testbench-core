<?php

namespace Orchestra\Testbench\Concerns;

trait WithLaravelMigrations
{
    /**
     * Migrate Laravel's default migrations.
     *
     * @param  array|string  $database
     *
     * @return void
     */
    protected function loadLaravelMigrations($database = []): void
    {
        $options = is_array($database) ? $database : ['--database' => $database];

        $options['--path'] = 'migrations';

        $this->artisan('migrate', $options);

        $this->resetApplicationArtisanCommands($this->app);

        $this->beforeApplicationDestroyed(function () use ($options) {
            $this->artisan('migrate:rollback', $options);
        });
    }
}
