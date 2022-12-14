<?php

namespace Orchestra\Testbench\Bootstrap;

use ErrorException;
use Exception;
use Illuminate\Log\LogManager;

final class HandleExceptions extends \Illuminate\Foundation\Bootstrap\HandleExceptions
{
    /**
     * Reports a deprecation to the "deprecations" logger.
     *
     * @param  string  $message
     * @param  string  $file
     * @param  int  $line
     * @param  int  $level
     * @return void
     */
    public function handleDeprecationError($message, $file, $line, $level = E_DEPRECATED)
    {
        if (! class_exists(LogManager::class)
            || ! static::$app->hasBeenBootstrapped()
        ) {
            return;
        }

        try {
            $logger = static::$app->make(LogManager::class);
        } catch (Exception $e) {
            return;
        }

        $this->ensureDeprecationLoggerIsConfigured();

        /** @phpstan-ignore-next-line */
        $options = static::$app['config']->get('logging.deprecations') ?? [];

        with($logger->channel('deprecations'), function ($log) use ($message, $file, $line, $level, $options) {
            $error = new ErrorException($message, 0, $level, $file, $line);

            if ($options['trace'] ?? false) {
                $log->warning((string) $error);
            } else {
                $log->warning(sprintf('%s in %s on line %s',
                    $message, $file, $line
                ));
            }
        });
    }
}
