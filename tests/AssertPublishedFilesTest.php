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
            "laravel/laravel",
        ], 'composer.json');

        $this->assertFileDoesNotContains([
            "orchestra/workbench",
        ], 'composer.json');

        $this->assertFileNotContains([
            "orchestra/workbench",
        ], 'composer.json');
    }

    /** @test */
    public function it_can_test_assert_file_exists()
    {
        $this->assertFilenameExists('composer.json');

        $this->assertFilenameDoesNotExists('composer.lock');
        $this->assertFilenameNotExists('composer.lock');
    }
}
