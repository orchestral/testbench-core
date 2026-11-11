<?php

namespace Orchestra\Testbench\Foundation\Process;

use Closure;
use Illuminate\Concurrency\Console\InvokeSerializedClosureCommand;
use Illuminate\Support\Traits\ForwardsCalls;
use Orchestra\Testbench\Foundation\Process\RemoteCommand\ConcurrencyEnvelope;
use RuntimeException;
use Symfony\Component\Process\Process;

use function Orchestra\Testbench\laravel_version_compare;

/**
 * @internal
 */
final class ProcessResult extends \Illuminate\Process\ProcessResult
{
    use ForwardsCalls;

    /**
     * The methods that should be returned from process instance.
     *
     * @var array<int, string>
     */
    protected array $passthru = [
        'getCommandLine',
        'getErrorOutput',
        'getExitCode',
        'getOutput',
        'isSuccessful',
    ];

    /**
     * Create a new process result instance.
     *
     * @param  \Symfony\Component\Process\Process  $process
     * @param  (\Closure():(mixed))|array<int, string>|string  $command
     */
    public function __construct(
        Process $process,
        protected Closure|array|string $command,
    ) {
        parent::__construct($process);
    }

    /** {@inheritDoc} */
    #[\Override]
    public function output()
    {
        $output = $this->process->getOutput();

        if (! $this->command instanceof Closure) {
            return $output;
        }

        if (laravel_version_compare('13.40.0', '>=')) {
            $key = $this->process->getEnv()['LARAVEL_CONCURRENCY_HMAC_KEY'] ?? null;

            if (! \is_string($key) || $key === '') {
                throw new RuntimeException('The remote process did not return a valid result envelope.');
            }

            try {
                $result = (new ConcurrencyEnvelope)->decrypt($output, $key);
            } catch (RuntimeException $exception) {
                if (method_exists(InvokeSerializedClosureCommand::class, 'frameEnvelope')) {
                    throw $exception;
                }

                $result = json_decode($output, true);

                if (! \is_array($result) || ! \array_key_exists('successful', $result)) {
                    throw $exception;
                }
            }
        } else {
            $result = json_decode($output, true);
        }

        /** @var array{successful: bool, result: string, exception: \Throwable, parameters: array, message: string} $result */
        if (! $result['successful']) {
            throw new $result['exception'](
                ...(! empty(array_filter($result['parameters']))
                    ? $result['parameters']
                    : [$result['message']])
            );
        }

        return unserialize($result['result']);
    }

    /**
     * Handle dynamic calls to the process instance.
     *
     * @param  string  $method
     * @param  array<int, mixed>  $parameters
     * @return mixed
     *
     * @throws \BadMethodCallException
     */
    public function __call($method, $parameters)
    {
        if (! \in_array($method, $this->passthru)) {
            self::throwBadMethodCallException($method);
        }

        return $this->forwardDecoratedCallTo($this->process, $method, $parameters);
    }
}
