<?php

namespace Orchestra\Testbench\Tests\Foundation\Actions;

use Illuminate\Foundation\Application;
use Orchestra\Testbench\Attributes\WithConfig;
use Orchestra\Testbench\Concerns\Database\InteractsWithSqliteDatabaseFile;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RequiresOperatingSystem;
use PHPUnit\Framework\Attributes\Test;

use function Orchestra\Testbench\remote;

#[Group('commander')]
#[RequiresOperatingSystem('Linux|DAR')]
#[WithConfig('app.key', 'SECXIvnK5r28GVIWUAxmbBSjTsmF')]
class RemoteCommandTest extends TestCase
{
    use InteractsWithSqliteDatabaseFile;

    #[Test]
    public function it_can_call_remote_and_get_current_version()
    {
        $this->withoutSqliteDatabase(function () {
            $process = remote(['--version', '--no-ansi']);
            $process->mustRun();

            $this->assertSame('Laravel Framework '.Application::VERSION.PHP_EOL, $process->getOutput());

            $process = remote(fn () => 1 + 1);
            $process->mustRun();

            $this->assertSame('{"successful":true,"result":"i:2;"}', $process->getOutput());
        });
    }
}
