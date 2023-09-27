<?php

namespace Orchestra\Testbench\Tests;

use Orchestra\Testbench\Concerns\InteractsWithPublishedFiles;
use Orchestra\Testbench\TestCase;

class AssertPublishedFilesTest extends TestCase
{
    use InteractsWithPublishedFiles;

    /** @test */
    public function it_can_test_assert_file_contains()
    {
        $this->assertFileContains([
            'laravel/laravel',
        ], 'composer.json');

        $this->assertFileDoesNotContains([
            'orchestra/workbench',
        ], 'composer.json');

        $this->assertFileNotContains([
            'orchestra/workbench',
        ], 'composer.json');
    }

    /** @test */
    public function it_can_test_assert_file_exists()
    {
        $this->assertFilenameExists('composer.json');

        $this->assertFilenameDoesNotExists('composer.lock');
        $this->assertFilenameNotExists('composer.lock');
    }

    /** @test */
    public function it_can_test_assert_migrations_files()
    {
        $this->assertMigrationFileContains([
            'return new class extends Migration',
            'Schema::create(\'users\', function (Blueprint $table) {',
        ], 'testbench_create_users_table.php', directory: 'migrations');

        $this->assertMigrationFileDoesNotContains([
            'class TestbenchCreateUsersTable extends Migration',
        ], 'testbench_create_users_table.php', directory: 'migrations');

        $this->assertMigrationFileExists('0001_01_01_000000_testbench_create_users_table.php', 'migrations');
        $this->assertMigrationFileDoesNotExists('0001_01_01_000000_create_users_table.php', 'migrations');
    }
}
