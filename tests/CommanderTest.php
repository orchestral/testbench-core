<?php

namespace Orchestra\Testbench\Tests;

use Illuminate\Console\Application as Artisan;
use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Application;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\Facade;
use Orchestra\Testbench\Console\Commander;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

class CommanderTest extends TestCase
{
    protected static array $variables = [
        'DB_CONNECTION',
    ];

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        collect(static::$variables)->each(function ($variable) {
            unset($_ENV[$variable], $_SERVER[$variable]);
        });

        Env::disablePutenv();
        Container::getInstance()->flush();
        Facade::clearResolvedInstances();
        Artisan::forgetBootstrappers();

        $this->dropSqliteDatabase();
    }

    /**
     * Teardown the test environment.
     */
    protected function tearDown(): void
    {
        collect(static::$variables)->each(function ($variable) {
            unset($_ENV[$variable], $_SERVER[$variable]);
        });

        Env::enablePutenv();
        Container::getInstance()->flush();
        Facade::clearResolvedInstances();
        Artisan::forgetBootstrappers();

        $this->dropSqliteDatabase();
    }

    /**
     * @test
     * @group commander
     */
    public function it_can_call_commander_using_cli()
    {
        $command = [static::phpBinary(), 'testbench', '--version', '--no-ansi'];

        $commander = Process::fromShellCommandline(implode(' ', $command), __DIR__.'/../', []);
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
        $this->assertFalse(file_exists(realpath(__DIR__.'/../').'/laravel/database/database.sqlite'));

        $command = [static::phpBinary(), 'testbench', 'about', '--json'];

        $commander = Process::fromShellCommandline(implode(' ', $command), __DIR__.'/../', []);
        $commander->mustRun();

        $output = json_decode($commander->getOutput(), true);

        $this->assertSame('Testbench', $output['environment']['application_name']);
        $this->assertSame('testing', $output['drivers']['database']);

        unset($commander);
    }

    /**
     * @test
     * @group commander
     */
    public function it_output_correct_defaults_with_database_file()
    {
        $this->createSqliteDatabase();

        $this->assertTrue(file_exists(realpath(__DIR__.'/../').'/laravel/database/database.sqlite'));

        $command = [static::phpBinary(), 'testbench', 'about', '--json'];

        $commander = Process::fromShellCommandline(implode(' ', $command), __DIR__.'/../');
        $commander->mustRun();

        $output = json_decode($commander->getOutput(), true);

        $this->assertSame('Testbench', $output['environment']['application_name']);
        $this->assertSame('sqlite', $output['drivers']['database']);

        unset($commander);
    }

    /**
     * @test
     * @group commander
     */
    public function it_output_correct_defaults_with_environment_overrides()
    {
        $this->createSqliteDatabase();

        $this->assertTrue(file_exists(realpath(__DIR__.'/../').'/laravel/database/database.sqlite'));

        $command = [static::phpBinary(), 'testbench', 'about', '--json'];

        $commander = Process::fromShellCommandline(implode(' ', $command), __DIR__.'/../', [
            'APP_NAME' => 'Testbench Tests',
            'DB_CONNECTION' => 'testing',
        ]);
        $commander->mustRun();

        $output = json_decode($commander->getOutput(), true);

        $this->assertSame('Testbench Tests', $output['environment']['application_name']);
        $this->assertSame('testing', $output['drivers']['database']);

        unset($commander);
    }

    /**
     * Drop Sqlite Database.
     */
    protected function createSqliteDatabase(): void
    {
        $filesystem = new Filesystem();

        $database = realpath(__DIR__.'/../').'/laravel/database/database.sqlite';

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
