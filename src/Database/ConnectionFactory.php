<?php

namespace Orchestra\Testbench\Database;

/**
 * @internal
 */
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
        /** @var string $name */
        $name = $name ?? $config['name'];

        if ($config['driver'] === 'sqlite') {
            return parent::make($config, $name);
        }

        if (! isset(static::$cachedConnections[$name]) || \is_null((static::$cachedConnections[$name]->getRawPdo() ?? null))) {
            return static::$cachedConnections[$name] = parent::make($config, $name);
        }

        $config = $this->parseConfig($config, $name);

        $connection = $this->createConnection(
            $config['driver'], static::$cachedConnections[$name]->getRawPdo(), $config['database'], $config['prefix'], $config
        )->setReadPdo(static::$cachedConnections[$name]->getRawReadPdo());

        return static::$cachedConnections[$name] = $connection;
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
