<?php

namespace Orchestra\Testbench\Tests;

use Illuminate\Foundation\Application;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

class CommanderTest extends TestCase
{
    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        $this->dropSqliteDatabase();
    }

    /**
     * Teardown the test environment.
     */
    protected function tearDown(): void
    {
        $this->dropSqliteDatabase();
    }

    /**
     * @test
     * @group commander
     */
    public function it_can_call_commander_using_cli()
    {
        $command = [$this->phpBinary(), 'testbench', '--version', '--no-ansi'];

        $commander = Process::fromShellCommandline(implode(' ', $command), __DIR__.'/../');
        $commander->mustRun();

        $this->assertSame('Laravel Framework '.Application::VERSION.PHP_EOL, $commander->getOutput());
    }

    /**
     * @test
     * @group commander
     */
    public function it_default_to_testing_when_database_isnt_available()
    {
        $command = [$this->phpBinary(), 'testbench', 'about', '--json'];

        $commander = Process::fromShellCommandline(implode(' ', $command), __DIR__.'/../');
        $commander->mustRun();

        $output = json_decode($commander->getOutput(), true);

        $this->assertSame('testing', $output['drivers']['database']);
    }

    /**
     * @test
     * @group commander
     */
    public function it_default_to_sqlite_when_database_is_available()
    {
        $this->createSqliteDatabase();

        $command = [$this->phpBinary(), 'testbench', 'about', '--json'];

        $commander = Process::fromShellCommandline(implode(' ', $command), __DIR__.'/../');
        $commander->mustRun();

        $output = json_decode($commander->getOutput(), true);

        $this->assertSame('testing', $output['drivers']['database']);
    }

    /**
     * Drop Sqlite Database.
     */
    protected function createSqliteDatabase(): void
    {
        $command = [$this->phpBinary(), 'testbench', 'package:create-sqlite-db'];

        $commander = Process::fromShellCommandline(implode(' ', $command), __DIR__.'/../');
        $commander->mustRun();
    }

    /**
     * Drop Sqlite Database.
     */
    protected function dropSqliteDatabase(): void
    {
        $command = [$this->phpBinary(), 'testbench', 'package:drop-sqlite-db'];

        $commander = Process::fromShellCommandline(implode(' ', $command), __DIR__.'/../');
        $commander->mustRun();
    }

    /**
     * PHP Binary path.
     */
    protected function phpBinary(): string
    {
        if (\defined('PHP_BINARY')) {
            return PHP_BINARY;
        }

        return \defined('PHP_BINARY') ? PHP_BINARY : (new PhpExecutableFinder())->find();
    }
}
