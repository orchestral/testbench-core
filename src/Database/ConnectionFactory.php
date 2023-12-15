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
        /** @var string $key */
        $key = $name ?? $config['name'];

        if ($config['driver'] === 'sqlite') {
            return parent::make($config, $name);
        }

        if (! isset(static::$cachedConnections[$key]) || \is_null((static::$cachedConnections[$key]->getRawPdo() ?? null))) {
            return static::$cachedConnections[$key] = parent::make($config, $name);
        }

        $config = $this->parseConfig($config, $name);

        $connection = $this->createConnection(
            $config['driver'], static::$cachedConnections[$key]->getRawPdo(), $config['database'], $config['prefix'], $config
        )->setReadPdo(static::$cachedConnections[$key]->getRawReadPdo());

        return static::$cachedConnections[$key] = $connection;
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
