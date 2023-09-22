<?php

namespace Orchestra\Testbench\Tests;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ProcessUtils;
use Orchestra\Testbench\Concerns\InteractsWithPublishedFiles;
use Orchestra\Testbench\TestCase;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

/**
 * @requires OS Linux|DAR
 */
class CommanderTest extends TestCase
{
    use InteractsWithPublishedFiles;

    protected $files = [];

    /**
     * @test
     *
     * @group commander
     */
    public function it_can_call_commander_using_cli_and_get_current_version()
    {
        $this->withoutSqliteDatabase(function () {
            $command = [$this->phpBinary(), 'testbench', '--version'];

            $process = $this->processFromShellCommandLine($command);
            $process->mustRun();

            $this->assertSame('Laravel Framework '.Application::VERSION.PHP_EOL, $process->getOutput());
        });
    }

    /**
     * @test
     *
     * @group commander
     */
    public function it_can_call_commander_using_cli_and_get_current_environment()
    {
        $this->withoutSqliteDatabase(function () {
            $command = [$this->phpBinary(), 'testbench', 'env'];

            $process = $this->processFromShellCommandLine($command, [
                'APP_ENV' => 'workbench',
            ]);
            $process->mustRun();

            $this->assertSame('Current application environment: workbench'.PHP_EOL, $process->getOutput());
        });
    }

    /**
     * @test
     *
     * @group commander
     */
    public function it_can_call_commander_using_cli_and_run_migration()
    {
        $this->withSqliteDatabase(function () {
            $command = [$this->phpBinary(), 'testbench', 'migrate'];

            $process = $this->processFromShellCommandLine($command, [
                'DB_CONNECTION' => 'sqlite',
            ]);

            $process->mustRun();

            $this->assertSame([
                '2013_07_26_182750_create_testbench_users_table',
                '2014_10_12_000000_testbench_create_users_table',
                '2014_10_12_100000_testbench_create_password_resets_table',
                '2019_08_19_000000_testbench_create_failed_jobs_table',
            ], DB::connection('sqlite')->table('migrations')->pluck('migration')->all());
        });
    }

    /**
     * @test
     *
     * @group commander
     */
    public function it_can_call_commander_using_cli_and_run_migration_without_default_migration()
    {
        $this->withSqliteDatabase(function () {
            $command = [$this->phpBinary(), 'testbench', 'migrate'];

            $process = $this->processFromShellCommandLine($command, [
                'DB_CONNECTION' => 'sqlite',
                'TESTBENCH_WITHOUT_DEFAULT_MIGRATIONS' => '(true)',
            ]);

            $process->mustRun();

            $this->assertSame([
                '2013_07_26_182750_create_testbench_users_table',
            ], DB::connection('sqlite')->table('migrations')->pluck('migration')->all());
        });
    }

    /**
     * Drop Sqlite Database.
     */
    protected function withoutSqliteDatabase(callable $callback): void
    {
        $time = time();
        $filesystem = new Filesystem();

        $database = __DIR__.'/../laravel/database/database.sqlite';

        if ($filesystem->exists($database)) {
            $filesystem->move($database, $temporary = "{$database}.backup-{$time}");
            array_push($this->files, $temporary);
        }

        value($callback);

        if (isset($temporary)) {
            $filesystem->move($temporary, $database);
        }
    }

    /**
     * Drop Sqlite Database.
     */
    protected function withSqliteDatabase(callable $callback): void
    {
        $this->withoutSqliteDatabase(function () use ($callback) {
            $filesystem = new Filesystem();

            $database = __DIR__.'/../laravel/database/database.sqlite';
            $time = time();

            if (! $filesystem->exists($database)) {
                $filesystem->copy($example = "{$database}.example", $database);
            }

            value($callback);

            if (isset($example)) {
                $filesystem->delete($database);
            }
        });
    }

    /**
     * Create Process from shell command line.
     *
     * @param  string|array<int, string>  $command
     * @param  array<string, mixed>  $variables
     * @return \Symfony\Component\Process\Process
     */
    protected function processFromShellCommandLine($command, array $variables = []): Process
    {
        $command = \is_array($command) ? implode(' ', $command) : $command;

        return Process::fromShellCommandline($command, __DIR__.'/../', $variables);
    }

    /**
     * PHP Binary path.
     */
    protected function phpBinary(): string
    {
        return transform(
            \defined('PHP_BINARY') ? PHP_BINARY : (new PhpExecutableFinder())->find(),
            function ($phpBinary) {
                return ProcessUtils::escapeArgument($phpBinary);
            }
        );
    }
}
