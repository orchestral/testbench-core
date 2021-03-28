<?php

namespace Orchestra\Testbench\Tests;

use Illuminate\Foundation\Application;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

class CommanderTest extends TestCase
{
    /** @test */
    public function it_can_call_commander()
    {
        $commander = Process::fromShellCommandline('./testbench --version', __DIR__.'/../');
        $commander->mustRun();

        $this->assertSame("Laravel Framework ".Application::VERSION.PHP_EOL, $commander->getOutput());
    }
}
