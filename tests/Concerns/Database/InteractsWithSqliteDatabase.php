<?php

namespace Orchestra\Testbench\Tests\Concerns\Database;

use Illuminate\Filesystem\Filesystem;
use Orchestra\Testbench\Concerns\InteractsWithPublishedFiles;

trait InteractsWithSqliteDatabase
{
    use InteractsWithPublishedFiles;

    /**
     * Drop Sqlite Database.
     */
    protected function withoutSqliteDatabase(callable $callback): void
    {
        if (! property_exists($this, 'files') || ! is_array($this->files)) {
            $this->files = [];
        }

        $time = time();
        $filesystem = new Filesystem();

        $database = __DIR__.'/../../../laravel/database/database.sqlite';

        if ($filesystem->exists($database)) {
            $filesystem->move($database, $temporary = "{$database}.backup-{$time}");

            array_push($this->files, $temporary);
        }

        value($callback);

        if (isset($temporary)) {
            $filesystem->move($temporary, $database);
        }
    }

    /**
     * Drop Sqlite Database.
     */
    protected function withSqliteDatabase(callable $callback): void
    {
        $this->withoutSqliteDatabase(function () use ($callback) {
            $filesystem = new Filesystem();

            $database = __DIR__.'/../../../laravel/database/database.sqlite';
            $time = time();

            if (! $filesystem->exists($database)) {
                $filesystem->copy($example = "{$database}.example", $database);
            }

            value($callback);

            if (isset($example)) {
                $filesystem->delete($database);
            }
        });
    }
}
