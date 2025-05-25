<?php

namespace Orchestra\Testbench\Bootstrap;

use Illuminate\Log\LogManager;
use Orchestra\Sidekick\Env;
use Orchestra\Testbench\Exceptions\DeprecatedException;

use function Orchestra\Sidekick\join_paths;
use function Orchestra\Sidekick\phpunit_version_compare;

/**
 * @internal
 */
final class HandleExceptions extends \Illuminate\Foundation\Bootstrap\HandleExceptions
{
    /**
     * Create a new exception handler instance.
     *
     * @param  \PHPUnit\Framework\TestCase|null  $testbench
     */
    public function __construct(
        protected $testbench = null
    ) {}

    /** {@inheritDoc} */
    #[\Override]
    public function handleDeprecationError($message, $file, $line, $level = E_DEPRECATED)
    {
        parent::handleDeprecationError($message, $file, $line, $level);

        $testbenchConvertDeprecationsToExceptions = Env::get('TESTBENCH_CONVERT_DEPRECATIONS_TO_EXCEPTIONS');

        $error = new DeprecatedException($message, $level, $file, $line);

        if ($testbenchConvertDeprecationsToExceptions === true) {
            throw $error;
        }

        if ($testbenchConvertDeprecationsToExceptions !== false && $this->getPhpUnitConvertDeprecationsToExceptions() === true) {
            throw $error;
        }
    }

    /**
     * Ensure the "deprecations" logger is configured.
     *
     * @return void
     */
    protected function ensureDeprecationLoggerIsConfigured()
    {
        with(self::$app['config'], static function ($config) { /** @phpstan-ignore offsetAccess.nonOffsetAccessible */
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

    /**
     * Get PHPUnit convert deprecations to exceptions from TestResult.
     *
     * @phpunit-overrides
     *
     * @return bool
     */
    protected function getPhpUnitConvertDeprecationsToExceptions(): bool
    {
        if (phpunit_version_compare('10', '>=')) {
            return false;
        }

        /** @var \PHPUnit\Framework\TestResult|null $testResult */
        $testResult = $this->testbench?->getTestResultObject();

        return $testResult?->getConvertDeprecationsToExceptions() ?? false;
    }

    /**
     * Determine if deprecation error should be ignored.
     *
     * @return bool
     */
    protected function shouldIgnoreDeprecationErrors()
    {
        return ! class_exists(LogManager::class)
            || ! self::$app->hasBeenBootstrapped()
            || ! Env::get('LOG_DEPRECATIONS_WHILE_TESTING', true);
    }
}
