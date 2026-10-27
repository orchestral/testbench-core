<?php

namespace Orchestra\Testbench\Tests\Foundation\Process;

use Illuminate\Filesystem\Filesystem;
use Orchestra\Testbench\Attributes\WithConfig;
use Orchestra\Testbench\Foundation\Process\RemoteWebServer;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RequiresOperatingSystem;
use PHPUnit\Framework\Attributes\Test;
use RuntimeException;
use Symfony\Component\Process\Process;

use function Orchestra\Testbench\default_skeleton_path;
use function Orchestra\Testbench\in_parallel_testing;
use function Orchestra\Testbench\phpunit_version_compare;

#[Group('commander')]
#[RequiresOperatingSystem('Linux|DAR')]
#[WithConfig('app.key', 'SECXIvnK5r28GVIWUAxmbBSjTsmF')]
class RemoteWebServerTest extends TestCase
{
    /**
     * Indicates whether the test has an environment file.
     *
     * @var bool
     */
    protected bool $hasEnvironmentFile = false;

    /** {@inheritDoc} */
    #[\Override]
    protected function setUp(): void
    {
        $filesystem = new Filesystem;

        if (! $filesystem->exists(default_skeleton_path('.env'))) {
            $this->beforeApplicationDestroyed(function () use ($filesystem) {
                $filesystem->delete(default_skeleton_path('.env'));
            });
        }

        parent::setUp();
    }

    /** {@inheritDoc} */
    protected function defineEnvironment($app)
    {
        $this->markTestSkippedWhen(in_parallel_testing(), 'Testbench CLI uses `.env` from skeleton instead of environment variables from PHPUnit');

        parent::defineEnvironment($app);
    }

    #[Test]
    public function it_can_start_and_stop_a_remote_web_server()
    {
        $server = new RemoteWebServer(port: $this->getAvailablePort());

        try {
            $server->start();

            $process = $server->getProcess();

            $this->assertInstanceOf(Process::class, $process);
            $this->assertTrue($process->isRunning());
            $this->assertSame('Hello world', file_get_contents("http://{$server->host}:{$server->port}/hello-world"));
        } finally {
            $server->stop();
        }

        $stoppedProcess = $server->getProcess();

        $this->assertInstanceOf(Process::class, $stoppedProcess);
        $this->assertFalse($stoppedProcess->isRunning());
    }

    #[Test]
    public function it_cannot_start_when_a_server_is_already_running_on_the_port()
    {
        $socket = stream_socket_server('tcp://127.0.0.1:0', $errorNumber, $errorString);

        $this->assertNotFalse($socket, 'Unable to reserve a local TCP port.');

        $address = stream_socket_get_name($socket, false);
        $this->assertIsString($address);

        $port = parse_url("tcp://{$address}", PHP_URL_PORT);
        $this->assertIsInt($port);

        try {
            $this->expectException(RuntimeException::class);

            if (phpunit_version_compare('13.3', '>=')) {
                $this->expectExceptionMessageIsOrContains("127.0.0.1:{$port}");
            } else {
                $this->expectExceptionMessage("127.0.0.1:{$port}");
            }

            (new RemoteWebServer(port: $port))->start();
        } finally {
            fclose($socket);
        }
    }

    /**
     * Get an available TCP port on the local machine.
     *
     * @return int
     */
    private function getAvailablePort(): int
    {
        $socket = stream_socket_server('tcp://127.0.0.1:0', $errorNumber, $errorString);

        $this->assertNotFalse($socket, 'Unable to reserve a local TCP port.');

        try {
            $address = stream_socket_get_name($socket, false);
            $this->assertIsString($address);

            $port = parse_url("tcp://{$address}", PHP_URL_PORT);
            $this->assertIsInt($port);

            return $port;
        } finally {
            fclose($socket);
        }
    }
}
