<?php

namespace Orchestra\Testbench\Concerns;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\Str;

trait DatabaseConnections
{
    /**
     * Allow to use database connections environment variables.
     */
    final protected function usesDatabaseConnectionsEnvironmentVariables(Repository $config, string $driver, string $keyword): void
    {
        $keyword = Str::upper($keyword);

        $config->set([
            "database.connections.{$driver}.url" => env("{$keyword}_URL", $config->get("database.connections.{$driver}.url")),
            "database.connections.{$driver}.host" => env("{$keyword}_HOST", $config->get("database.connections.{$driver}.host")),
            "database.connections.{$driver}.port" => env("{$keyword}_PORT", $config->get("database.connections.{$driver}.port")),
            "database.connections.{$driver}.database" => env("{$keyword}_DATABASE", $config->get("database.connections.{$driver}.database")),
            "database.connections.{$driver}.username" => env("{$keyword}_USERNAME", $config->get("database.connections.{$driver}.username")),
            "database.connections.{$driver}.password" => env("{$keyword}_PASSWORD", $config->get("database.connections.{$driver}.password")),
        ]);
    }
}
