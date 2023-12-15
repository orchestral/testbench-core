<?php

namespace Orchestra\Testbench\Database;

class ConnectionFactory extends \Illuminate\Database\Connectors\ConnectionFactory
{
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
        $config = $this->parseConfig($config, $name);

        if (isset($config['read'])) {
            return $this->createReadWriteConnection($config);
        }

        return $this->createSingleConnection($config);
    }
}
