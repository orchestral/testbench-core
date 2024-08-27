<?php

namespace Orchestra\Testbench\Tests\Foundation\Bootstrap;

use Illuminate\Filesystem\Filesystem;
use Orchestra\Testbench\Attributes\UsesVendor;
use Orchestra\Testbench\Foundation\Bootstrap\CreateVendorSymlink;
use Orchestra\Testbench\TestCase;

use function Orchestra\Testbench\container;
use function Orchestra\Testbench\laravel_vendor_exists;
use function Orchestra\Testbench\package_path;

/**
 * @requires OS Linux|DAR
 */
class CreateVendorSymlinkTest extends TestCase
{
    /** @test */
    public function it_can_create_vendor_symlink()
    {
        $workingPath = package_path('vendor');

        $laravel = container()->createApplication();

        if (laravel_vendor_exists($laravel, $workingPath)) {
            (new Filesystem)->delete($laravel->basePath('vendor'));
        }

        (new CreateVendorSymlink($workingPath))->bootstrap($laravel);

        $this->assertTrue($laravel['TESTBENCH_VENDOR_SYMLINK']);
    }

    /** @test */
    #[UsesVendor]
    public function it_can_skip_existing_vendor_symlink()
    {
        $laravel = container()->createApplication();

        (new CreateVendorSymlink(package_path('vendor')))->bootstrap($laravel);

        $this->assertFalse($laravel['TESTBENCH_VENDOR_SYMLINK']);
    }
}
