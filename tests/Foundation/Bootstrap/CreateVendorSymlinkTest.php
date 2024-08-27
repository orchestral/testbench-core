<?php

namespace Orchestra\Testbench\Tests\Foundation\Bootstrap;

use Illuminate\Filesystem\Filesystem;
use Orchestra\Testbench\Attributes\UsesVendor;
use Orchestra\Testbench\Foundation\Bootstrap\CreateVendorSymlink;
use Orchestra\Testbench\TestCase;

use function Orchestra\Testbench\container;
use function Orchestra\Testbench\package_path;

/**
 * @requires OS Linux|DAR
 */
class CreateVendorSymlinkTest extends TestCase
{
    /** @test */
    public function it_can_create_vendor_symlink()
    {
        $filesystem = new Filesystem;

        $stub = (new CreateVendorSymlink(package_path('vendor')));

        $laravel = container()->createApplication();

        if ($stub->vendorSymlinkExists($laravel)) {
            $filesystem->delete($laravel->basePath('vendor'));
        }

        $stub->bootstrap($laravel);

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
