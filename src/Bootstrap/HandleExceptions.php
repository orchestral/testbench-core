<?php

namespace Orchestra\Testbench\Bootstrap;

use Illuminate\Log\LogManager;
use Orchestra\Testbench\Exceptions\DeprecatedException;
use Orchestra\Testbench\Foundation\Env;

use function Orchestra\Testbench\join_paths;

/**
 * @internal
 */
final class HandleExceptions extends \Illuminate\Foundation\Bootstrap\HandleExceptions
{
    /**
     * {@inheritDoc}
     *
     * @throws \Orchestra\Testbench\Exceptions\DeprecatedException
     */
    #[\Override]
    public function handleDeprecationError($message, $file, $line, $level = E_DEPRECATED)
    {
        parent::handleDeprecationError($message, $file, $line, $level);

        $testbenchConvertDeprecationsToExceptions = Env::get('TESTBENCH_CONVERT_DEPRECATIONS_TO_EXCEPTIONS', false);

        if ($testbenchConvertDeprecationsToExceptions === true) {
            throw new DeprecatedException($message, $level, $file, $line);
        }
    }

    /** {@inheritDoc} */
    #[\Override]
    protected function ensureDeprecationLoggerIsConfigured()
    {
        with(self::$app->make('config'), static function ($config) {
            /** @var \Illuminate\Contracts\Config\Repository $config */
            if ($config->get('logging.channels.deprecations')) {
                return;
            }

            /** @var array{channel?: string, trace?: bool}|string|null $options */
            $options = $config->get('logging.deprecations');
            $trace = Env::get('LOG_DEPRECATIONS_TRACE', false);

            if (\is_array($options)) {
                $driver = $options['channel'] ?? 'null';
                $trace = $options['trace'] ?? true;
            } else {
                $driver = $options ?? 'null';
            }

            if ($driver === 'single') {
                $config->set('logging.channels.deprecations', array_merge($config->get('logging.channels.single'), [
                    'path' => self::$app->storagePath(join_paths('logs', 'deprecations.log')),
                ]));
            } else {
                $config->set('logging.channels.deprecations', $config->get("logging.channels.{$driver}"));
            }

            $config->set('logging.deprecations', [
                'channel' => 'deprecations',
                'trace' => $trace,
            ]);
        });
    }

    /** {@inheritDoc} */
    #[\Override]
    protected function shouldIgnoreDeprecationErrors()
    {
        return ! class_exists(LogManager::class)
            || ! self::$app->hasBeenBootstrapped()
            || ! Env::get('LOG_DEPRECATIONS_WHILE_TESTING', true);
    }
}
