<?php

namespace Orchestra\Testbench\Database;

use Illuminate\Container\Container;
use Illuminate\Database\Connectors\ConnectionFactory as BaseFactory;

/**
 * @internal
 */
class ConnectionFactory extends BaseFactory
{
    /**
     * List of cached database connections.
     *
     * @var array<string, \Illuminate\Database\Connection>
     */
    protected static array $cachedConnections = [];

    public function __construct(
        Container $container,
        protected BaseFactory $baseFactory
    ) {
        parent::__construct($container);
    }

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
            return $baseFactory->make($config, $name); // @phpstan-ignore-line
        }

        if (! isset(static::$cachedConnections[$key]) || \is_null((static::$cachedConnections[$key]->getRawPdo() ?? null))) {
            return static::$cachedConnections[$key] = $baseFactory->make($config, $name); // @phpstan-ignore-line
        }

        $config = $this->parseConfig($config, $name); // @phpstan-ignore-line

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
