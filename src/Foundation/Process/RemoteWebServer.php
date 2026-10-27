<?php

namespace Orchestra\Testbench\Foundation\Process;

use RuntimeException;

use function Orchestra\Testbench\remote;

class RemoteWebServer
{
    /**
     * The start/stop timeout for the remote web server in seconds.
     *
     * @var int
     */
    private const int START_STOP_TIMEOUT = 10;

    /**
     * The process decorator instance.
     *
     * @var \Orchestra\Testbench\Foundation\Process\ProcessDecorator|null
     */
    protected ?ProcessDecorator $process = null;

    /**
     * Construct a new web server.
     *
     * @param  string  $host
     * @param  int  $port
     * @param  int  $timeout
     */
    public function __construct(
        public readonly string $host = '127.0.0.1',
        public readonly int $port = 8000,
        public readonly int $timeout = 6000,
    ) {}

    /**
     * Start a php server in a separate process.
     *
     * @return void
     *
     * @throws \RuntimeException
     */
    public function start(): void
    {
        $this->stop();

        $this->handle();

        register_shutdown_function(function (): void {
            $this->stop();
        });
    }

    /**
     * Stop the php server.
     *
     * @return void
     */
    public function stop(): void
    {
        if (! $this->process instanceof ProcessDecorator) {
            return;
        }

        $this->process->stop();
    }

    /**
     * Handle the web server startup process.
     *
     * @return void
     */
    protected function handle(): void
    {
        $this->guardHostAndPortBeforeStartingWebServer();

        $this->createWebServerProcess();

        $this->waitUntilWebserverIsRunning();
    }

    /**
     * Clear the php server output.
     *
     * No-op when output was disabled at start (the default), since
     * Symfony Process redirects those streams to /dev/null.
     *
     * @return void
     */
    public function clearOutput(): void
    {
        if (\is_null($this->process) || $this->process->isOutputDisabled()) {
            return;
        }

        $this->process->clearOutput();
        $this->process->clearErrorOutput();
    }

    /**
     * Get the current process.
     *
     * @return \Orchestra\Testbench\Foundation\Process\ProcessDecorator|null
     */
    public function getProcess(): ?ProcessDecorator
    {
        return $this->process;
    }

    /**
     * Create and start the web server process.
     *
     * @return void
     */
    protected function createWebServerProcess(): void
    {
        $this->process = remote("serve --host={$this->host} --port={$this->port}"
            ->setTimeout($this->timeout)
            ->disableOutput();

        $this->process->start();
    }

    /**
     * Wait until the web server is running.
     */
    protected function waitUntilWebserverIsRunning(): void
    {
        $deadline = hrtime(true) + self::START_STOP_TIMEOUT * 1_000_000_000;

        do {
            $socket = @fsockopen($this->host, $this->port, $errorNumber, $errorString, 0.1);

            if ($socket !== false) {
                fclose($socket);

                return;
            }

            if (\is_null($this->process) || ! $this->process->isRunning()) {
                $output = trim($this->process?->getErrorOutput().$this->process?->getOutput());

                throw new RuntimeException(\sprintf(
                    'Unable to start web server on %s:%d.%s',
                    $this->host,
                    $this->port,
                    $output === '' ? '' : "\n{$output}",
                ));
            }

            usleep(50_000);
        } while (hrtime(true) < $deadline);

        throw new RuntimeException(\sprintf(
            'Timed out waiting for web server on %s:%d after %d seconds.',
            $this->host,
            $this->port,
            self::START_STOP_TIMEOUT,
        ));
    }

    /**
     * Verify that there isn't an existing server on the host and port
     * that we want to use.  Sometimes a server can be left oped when
     * PHP drops out, or the user may have another service running.
     *
     * @throws \RuntimeException
     */
    protected function guardHostAndPortBeforeStartingWebServer(): void
    {
        /** @var resource|null $socket */
        $socket = rescue(function () {
            $errorNumber = 0;
            $errorString = '';
            $timeout = 1;

            return @fsockopen($this->host, $this->port, $errorNumber, $errorString, $timeout);
        }, null, false);

        if ($socket) {
            fclose($socket);

            $this->throwUnableToStartWebServer();
        }
    }

    /**
     * Throw when another server is already running on the configured host and port.
     *
     * @throws \RuntimeException
     */
    private function throwUnableToStartWebServer(): never
    {
        throw new RuntimeException(
            "A server is already running on {$this->host}:{$this->port}. Please stop it before proceeding. \n".
            "It is likely to be a php server, so you can search your existing processes for 'php -L'. \n".
            'Alternatively you can change the setup for your tests to use a different host or port.'
        );
    }
}
