<?php

namespace Orchestra\Testbench\Tests;

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\DB;
use Orchestra\Testbench\Concerns\Database\InteractsWithSqliteDatabaseFile;
use Orchestra\Testbench\TestCase;

use function Orchestra\Testbench\remote;

/**
 * @requires OS Linux|DAR
 */
class CommanderTest extends TestCase
{
    use InteractsWithSqliteDatabaseFile;

    /**
     * @test
     *
     * @group commander
     */
    public function it_can_call_commander_using_cli_and_get_current_version()
    {
        $this->withoutSqliteDatabase(function () {
            $process = remote('--version --no-ansi');
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
            $process = remote('env', ['APP_ENV' => 'workbench']);
            $process->mustRun();

            $this->assertSame('INFO  The application environment is [workbench].', trim($process->getOutput()));
        });
    }

    /**
     * @test
     *
     * @group commander
     */
    public function it_output_correct_defaults()
    {
        $this->withoutSqliteDatabase(function () {
            $process = remote('about --json');
            $process->mustRun();

            $output = json_decode($process->getOutput(), true);

            $this->assertSame('Testbench', $output['environment']['application_name']);
            $this->assertSame('ENABLED', $output['environment']['debug_mode']);
            $this->assertSame('testing', $output['drivers']['database']);
        });
    }

    /**
     * @test
     *
     * @group commander
     */
    public function it_output_correct_defaults_with_database_file()
    {
        $this->withSqliteDatabase(function () {
            $process = remote('about --json');
            $process->mustRun();

            $output = json_decode($process->getOutput(), true);

            $this->assertSame('Testbench', $output['environment']['application_name']);
            $this->assertSame('ENABLED', $output['environment']['debug_mode']);
            $this->assertSame('sqlite', $output['drivers']['database']);
        });
    }

    /**
     * @test
     *
     * @group commander
     */
    public function it_output_correct_defaults_with_environment_overrides()
    {
        $this->withSqliteDatabase(function () {
            $process = remote('about --json', [
                'APP_NAME' => 'Testbench Tests',
                'APP_DEBUG' => '(false)',
                'DB_CONNECTION' => 'testing',
            ]);
            $process->mustRun();

            $output = json_decode($process->getOutput(), true);

            $this->assertSame('Testbench Tests', $output['environment']['application_name']);
            $this->assertSame('OFF', $output['environment']['debug_mode']);
            $this->assertSame('testing', $output['drivers']['database']);
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
            $process = remote('migrate', ['DB_CONNECTION' => 'sqlite']);

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
            $process = remote('migrate', [
                'DB_CONNECTION' => 'sqlite',
                'TESTBENCH_WITHOUT_DEFAULT_MIGRATIONS' => '(true)',
            ]);

            $process->mustRun();

            $this->assertSame([
                '2013_07_26_182750_create_testbench_users_table',
            ], DB::connection('sqlite')->table('migrations')->pluck('migration')->all());
        });
    }
}
