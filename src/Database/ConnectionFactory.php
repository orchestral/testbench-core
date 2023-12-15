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

        if (isset(static::$cachedConnections[$name]) && \is_null(static::$cachedConnections[$name]->getRawPdo())) {
            unset(static::$cachedConnections[$name]);
        }

        return static::$cachedConnections[$name] ??= parent::make($config, $name);
    }

    /**
     * Flush the current state.
     *
     * @return void
     */
    public static function flushState(): void
    {
        static::$cachedConnections = [];
    }
}
