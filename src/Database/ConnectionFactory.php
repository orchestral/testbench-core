<?php

namespace Orchestra\Testbench\Database;

class ConnectionFactory extends \Illuminate\Database\Connectors\ConnectionFactory
{
    /**
     * List of cached database connections.
     *
     * @var array<string, \Illuminate\Database\Connection>
     */
    protected static array $cachedConnections = [];

    /**
     * Establish a PDO connection based on the configuration.
     *
     * @param  array  $config
     * @param  string|null  $name
     * @return \Illuminate\Database\Connection
     */
    #[\Override]
    public function make(array $config, $name = null)
    {
        $connection = $name ?? $config['name'];

        return static::$cachedConnections[$name] ??= parent::make($config, $name);
    }
}
