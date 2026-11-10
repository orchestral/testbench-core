<?php

namespace Orchestra\Testbench\Tests\Foundation\Process;

use Illuminate\Foundation\Application;
use Orchestra\Testbench\Attributes\RequiresLaravel;
use Orchestra\Testbench\Attributes\WithConfig;
use Orchestra\Testbench\Concerns\Database\InteractsWithSqliteDatabaseFile;
use Orchestra\Testbench\Foundation\Process\ProcessDecorator;
use Orchestra\Testbench\Foundation\Process\ProcessResult;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Process\Process;

use function Orchestra\Testbench\in_parallel_testing;
use function Orchestra\Testbench\laravel_version_compare;
use function Orchestra\Testbench\remote;

#[Group('commander')]
#[RequiresOperatingSystem('Linux|DAR')]
#[WithConfig('app.key', 'SECXIvnK5r28GVIWUAxmbBSjTsmF')]
class RemoteCommandTest extends TestCase
{
    use InteractsWithSqliteDatabaseFile;

    /** {@inheritDoc} */
    protected function defineEnvironment($app)
    {
        $this->markTestSkippedWhen(in_parallel_testing(), 'Testbench CLI uses `.env` from skeleton instead of environment variables from PHPUnit');

        parent::defineEnvironment($app);
    }

    #[Test]
    public function it_can_call_remote_and_get_current_version()
    {
        $this->withoutSqliteDatabase(function () {
            $process = remote(['--version', '--no-ansi']);
            $result = $process->mustRun();

            $this->assertInstanceOf(ProcessDecorator::class, $process);
            $this->assertInstanceOf(ProcessResult::class, $result);
            $this->assertSame('Laravel Framework '.Application::VERSION.PHP_EOL, $process->getOutput());
            $this->assertSame('Laravel Framework '.Application::VERSION.PHP_EOL, $result->output());

            $process = remote(function () {
                return 1 + 1;
            });

            $result = $process->mustRun();

            $this->assertInstanceOf(ProcessDecorator::class, $process);
            $this->assertInstanceOf(ProcessResult::class, $result);

            if (laravel_version_compare('13.40.0', '>=')) {
                $this->assertMatchesRegularExpression('/^[a-f0-9]{64}$/', $process->process->getEnv()['LARAVEL_CONCURRENCY_HMAC_KEY']);
            } else {
                $this->assertSame('{"successful":true,"result":"i:2;"}', $process->getOutput());
            }

            $this->assertSame(2, $result->output());
        });
    }

    #[Test]
    #[RequiresLaravel('>=13.40.0')]
    public function it_can_read_an_authenticated_result_envelope()
    {
        $key = bin2hex(random_bytes(32));
        $encoded = base64_encode(json_encode([
            'successful' => true,
            'result' => serialize('result'),
        ]));
        $signature = hash_hmac('sha256', $encoded, $key);
        $output = "\n@@LARAVEL-CONCURRENCY-V1@@{$encoded}@@{$signature}@@\n";
        $process = new Process([
            PHP_BINARY,
            '-r',
            'echo base64_decode($argv[1]);',
            base64_encode($output),
        ], null, ['LARAVEL_CONCURRENCY_HMAC_KEY' => $key]);
        $process->run();

        $result = new ProcessResult($process, fn () => null);

        $this->assertSame('result', $result->output());
    }

    #[Test]
    #[RequiresLaravel('>=13.40.0')]
    public function it_rejects_an_unauthenticated_result_envelope()
    {
        $key = bin2hex(random_bytes(32));
        $encoded = base64_encode(json_encode([
            'successful' => true,
            'result' => serialize('forged'),
        ]));
        $output = "\n@@LARAVEL-CONCURRENCY-V1@@{$encoded}@@".str_repeat('0', 64)."@@\n";
        $process = new Process([
            PHP_BINARY,
            '-r',
            'echo base64_decode($argv[1]);',
            base64_encode($output),
        ], null, ['LARAVEL_CONCURRENCY_HMAC_KEY' => $key]);
        $process->run();
        $result = new ProcessResult($process, fn () => null);

        $this->expectException(\RuntimeException::class);

        $result->output();
    }
}
