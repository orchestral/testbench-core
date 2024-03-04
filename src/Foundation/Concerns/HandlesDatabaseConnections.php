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
            'url' => ['env' => 'URL', 'rules' => static fn ($value) => ! empty($value) && \is_string($value)],
            'host' => ['env' => 'HOST', 'rules' => static fn ($value) => ! empty($value) && \is_string($value)],
            'port' => ['env' => 'PORT', 'rules' => static fn ($value) => ! empty($value) && \is_int($value)],
            'database' => ['env' => ['DB', 'DATABASE'], 'rules' => static fn ($value) => ! empty($value) && \is_string($value)],
            'username' => ['env' => ['USER', 'USERNAME'], 'rules' => static fn ($value) => ! empty($value) && \is_string($value)],
            'password' => ['env' => 'PASSWORD', 'rules' => static fn ($value) => \is_null($value) || \is_string($value)],
            'collation' => ['env' => 'COLLATION', 'rules' => static fn ($value) => \is_null($value) || \is_string($value)],
        ];

        $config->set(
            Collection::make($options)
                ->when($driver === 'pgsql', static function ($options) {
                    return $options->put('schema', [
                        'env' => 'SCHEMA',
                        'rules' => static fn ($value) => ! empty($value) && \is_string($value),
                    ]);
                })
                ->mapWithKeys(static function ($options, $key) use ($driver, $keyword, $config) {
                    $name = "database.connections.{$driver}.{$key}";

                    /** @var mixed $configuration */
                    $configuration = Collection::make(Arr::wrap($options['env']))
                        ->transform(static fn ($value) => Env::get("{$keyword}_{$value}"))
                        ->first($options['rules'] ?? static fn ($value) => ! is_null($value)) ?? $config->get($name);

                    return [
                        "{$name}" => $configuration,
                    ];
                })->all()
        );
    }
}
