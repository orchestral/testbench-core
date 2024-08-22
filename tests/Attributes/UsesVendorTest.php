<?php

namespace Orchestra\Testbench\Tests\Attributes;

use Illuminate\Filesystem\Filesystem;
use Orchestra\Testbench\Attributes\UsesVendor;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;

use function Orchestra\Testbench\join_paths;
use function Orchestra\Testbench\package_path;

class UsesVendorTest extends TestCase
{
    #[Test]
    #[UsesVendor]
    public function it_can_uses_vendor_attribute()
    {
        $filesystem = new Filesystem;

        $this->assertSame(
            $filesystem->hash(base_path(join_paths('vendor', 'autoload.php'))),
            $filesystem->hash(package_path('vendor', 'autoload.php'))
        );
    }
}
