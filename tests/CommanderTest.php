<?php

namespace Orchestra\Testbench\Tests;

use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Facade;
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
        Container::getInstance()->flush();
        Facade::clearResolvedInstances();

        $this->dropSqliteDatabase();
    }

    /**
     * Teardown the test environment.
     */
    protected function tearDown(): void
    {
        Container::getInstance()->flush();
        Facade::clearResolvedInstances();

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

        unset($commander);
    }

    /**
     * @test
     * @group commander
     */
    public function it_output_correct_defaults()
    {
        $this->assertFalse(file_exists(__DIR__.'/../laravel/database/database.sqlite'));

        $command = [$this->phpBinary(), 'testbench', 'about', '--json'];

        $commander = Process::fromShellCommandline(implode(' ', $command), __DIR__.'/../');
        $commander->mustRun();

        $output = json_decode($commander->getOutput(), true);

        $this->assertSame('testing', $output['drivers']['database']);

        unset($commander);
    }

    /**
     * Drop Sqlite Database.
     */
    protected function createSqliteDatabase(): void
    {
        $filesystem = new Filesystem();

        $database = __DIR__.'/../laravel/database/database.sqlite';

        if (! $filesystem->exists($database)) {
            $filesystem->copy("{$database}.example", $database);
        }
    }

    /**
     * Drop Sqlite Database.
     */
    protected function dropSqliteDatabase(): void
    {
        $filesystem = new Filesystem();

        $database = __DIR__.'/../laravel/database/database.sqlite';

        if ($filesystem->exists($database)) {
            $filesystem->delete($database);
        }
    }

    /**
     * PHP Binary path.
     */
    public static function phpBinary(): string
    {
        if (\defined('PHP_BINARY')) {
            return PHP_BINARY;
        }

        return \defined('PHP_BINARY') ? PHP_BINARY : (new PhpExecutableFinder())->find();
    }
}
