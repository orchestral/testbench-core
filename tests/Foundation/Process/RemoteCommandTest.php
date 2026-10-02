<?php

namespace Orchestra\Testbench\Tests\Foundation\Process;

use Illuminate\Foundation\Application;
use Orchestra\Testbench\Attributes\WithConfig;
use Orchestra\Testbench\Concerns\Database\InteractsWithSqliteDatabaseFile;
use Orchestra\Testbench\Foundation\Process\ProcessDecorator;
use Orchestra\Testbench\Foundation\Process\ProcessResult;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;

use function Orchestra\Testbench\in_parallel_testing;
use function Orchestra\Testbench\remote;

/**
 * @requires OS Linux|DAR
 *
 * @group commander
 */
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

    /** @test */
    public function it_can_call_remote_and_get_current_version()
    {
        $this->withoutSqliteDatabase(function () {
            $process = remote(['--version', '--no-ansi']);
            $result = $process->mustRun();

            $this->assertInstanceOf(ProcessDecorator::class, $process);
            $this->assertInstanceOf(ProcessResult::class, $result);
            $this->assertSame('Laravel Framework '.Application::VERSION.PHP_EOL, $process->getOutput());
            $this->assertSame('Laravel Framework '.Application::VERSION.PHP_EOL, $result->output());
        });
    }
}
