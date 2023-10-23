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

        $this->assertMigrationFileExists('2014_10_12_100000_testbench_create_password_reset_tokens_table.php', 'migrations');
        $this->assertMigrationFileDoesNotExists('2014_10_12_100000_create_password_resets_tokens_table.php', 'migrations');
    }
}
