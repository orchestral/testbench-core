<?php

namespace Orchestra\Testbench\Tests;

use Illuminate\Foundation\Application;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

class CommanderTest extends TestCase
{
    /**
     * @test
     * @group commander
     */
    public function it_can_call_commander_using_cli()
    {
        $command = [$this->phpBinary(), 'testbench', '--version'];

        $commander = Process::fromShellCommandline(implode(' ', $command), __DIR__.'/../');
        $commander->mustRun();

        $this->assertSame("Laravel Framework ".Application::VERSION.PHP_EOL, $commander->getOutput());
    }

    /**
     * PHP Binary path.
     */
    protected function phpBinary(): string
    {
        if (defined('PHP_BINARY')) {
            return PHP_BINARY;
        }

        return defined('PHP_BINARY') ? PHP_BINARY : (new PhpExecutableFinder())->find();
    }
}
