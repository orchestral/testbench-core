<?php

namespace Orchestra\Testbench\Foundation\Process;

use RuntimeException;
use Symfony\Component\Process\Process;

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
     * @var \Symfony\Component\Process\Process|null
     */
    protected ?Process $process = null;

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

        $this->startServer();

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
        if (is_null($this->process)) {
            return;
        }

        $this->process->stop();
    }

    /**
     * Handle the web server startup process.
     *
     * @return void
     */
    protected function startServer(): void
    {
        $this->guardHostAndPortBeforeStartingWebServer();

        $this->process = $this->createWebServerProcess();

        $this->process->start();
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
     * @return \Symfony\Component\Process\Process|null
     */
    public function getProcess(): ?Process
    {
        return $this->process;
    }

    /**
     * Create and start the web server process.
     *
     * @return \Symfony\Component\Process\Process
     */
    protected function createWebServerProcess(): Process
    {
        return remote("serve --host={$this->host} --port={$this->port}")
            ->setTimeout($this->timeout)
            ->disableOutput()
            ->process;
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
    protected function throwUnableToStartWebServer(): never
    {
        throw new RuntimeException(
            "A server is already running on {$this->host}:{$this->port}. Please stop it before proceeding. \n".
            "It is likely to be a php server, so you can search your existing processes for 'php -L'. \n".
            'Alternatively you can change the setup for your tests to use a different host or port.'
        );
    }
}
