<?php

namespace Orchestra\Testbench\Tests\Foundation\Bootstrap;

use Illuminate\Filesystem\Filesystem;
use Orchestra\Testbench\Foundation\Bootstrap\CreateVendorSymlink;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;

use function Orchestra\Testbench\container;
use function Orchestra\Testbench\laravel_vendor_exists;
use function Orchestra\Testbench\package_path;

class CreateVendorSymlinkTest extends TestCase
{
    #[Test]
    public function it_can_create_vendor_symlink()
    {
        $workingPath = package_path('vendor');

        $laravel = container()->createApplication();

        $stub = (new CreateVendorSymlink($workingPath));

        if (laravel_vendor_exists($laravel, $workingPath)) {
            $stub->deleteVendorSymlink($laravel);
        }

        $stub->bootstrap($laravel);

        $this->assertTrue($laravel['TESTBENCH_VENDOR_SYMLINK']);
    }

    #[Test]
    public function it_can_skip_existing_vendor_symlink()
    {
        $workingPath = package_path('vendor');

        $laravel = container()->createApplication();

        if (! laravel_vendor_exists($laravel, $workingPath)) {
            (new Filesystem)->link($workingPath, $laravel->basePath('vendor'));
        }

        (new CreateVendorSymlink($workingPath))->bootstrap($laravel);

        $this->assertFalse($laravel['TESTBENCH_VENDOR_SYMLINK']);
    }
}
