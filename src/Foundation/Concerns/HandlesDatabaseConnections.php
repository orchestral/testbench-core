<?php

namespace Orchestra\Testbench\Foundation\Concerns;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Orchestra\Testbench\Foundation\Env;

trait HandlesDatabaseConnections
{
    /**
     * Allow to use database connections environment variables.
     */
    final protected function usesDatabaseConnectionsEnvironmentVariables(Repository $config, string $driver, string $keyword): void
    {
        $keyword = Str::upper($keyword);

        $options = [
            'url' => 'URL',
            'host' => 'HOST',
            'port' => 'PORT',
            'database' => ['DB', 'DATABASE'],
            'username' => ['USER', 'USERNAME'],
            'password' => 'PASSWORD',
            'collation' => 'COLLATION',
        ];

        $config->set(
            Collection::make($options)
                ->when($driver === 'pgsql', static function ($options) {
                    return $options->put('schema', 'SCHEMA');
                })
                ->mapWithKeys(static function ($value, $key) use ($driver, $keyword, $config) {
                    $name = "database.connections.{$driver}.{$key}";

                    /** @var mixed $configuration */
                    $configuration = Collection::make(Arr::wrap($value))
                        ->transform(static function ($value) use ($keyword) {
                            return Env::get("{$keyword}_{$value}");
                        })->first(static function ($value) {
                            return ! \is_null($value);
                        }) ?? $config->get($name);

                    return [
                        "{$name}" => $configuration,
                    ];
                })->all()
        );
    }
}
